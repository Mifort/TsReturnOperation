<?php

namespace v3\Operations;



use Exception;
use v3\Contractors\Client;
use v3\Contractors\Contractor;
use v3\Contractors\Employee;
use v3\Contractors\Seller;
use v3\Messengers\Messenger;

class TsReturnOperation extends ReferencesOperation
{

     private const TYPE_NEW    = 1;
     private const TYPE_CHANGE = 2;


    /**
     * @throws Exception
     */
    public function doOperation(): array
    {
        $data = $this->getRequest('data');
        // здесь привели к int, дальше уже не надо приводить

        $resellerId = (int)$data['resellerId'];
        $notificationType = (int)$data['notificationType'];

        $result = [
            'notificationEmployeeByEmail' => false,
            'notificationClientByEmail'   => false,
            'notificationClientBySms'     => [
                'isSent'  => false,
                'message' => '',
            ],
        ];

        if (empty($resellerId)) {
            throw new Exception('Empty resellerId', 400);

        }
        if (empty($notificationType)) {
            throw new Exception('Empty notificationType', 400);
        }

        // формируем потенциальных получателей писем(продавцов, клиентов, создателей, экспертов)
        $seller = Seller::getById($resellerId);
        if ($seller === null ) {
            throw new Exception('Seller not found!', 400);
        }
        $client = Client::getById($data['clientId']);
        if ($client === null || $client->getType() !== Contractor::TYPE_CUSTOMER || $client->getSellerId() !== $resellerId) {
            throw new Exception('client not found!', 400);
        }

        $creator = Employee::getById((int)$data['creatorId']);
        if ($creator === null) {
            throw new Exception('Creator not found!', 400);
        }

        $expert = new Employee((int)$data['expertId']);
        if ($expert === null) {
            throw new Exception('Expert not found!', 400);
        }
// в зависимости от типа сообщения получаем какие-то различия
        $differences = '';
        if ($notificationType === self::TYPE_NEW) {
            $differences = $this->__('NewPositionAdded', null, $resellerId);
        } elseif ($notificationType === self::TYPE_CHANGE && !empty($data['differences'])) {
            $differences = __('PositionStatusHasChanged', [
                'FROM' => Status::getName((int)$data['differences']['from']),
                'TO'   => Status::getName((int)$data['differences']['to']),
            ], $resellerId);
        }
// формируем данные для отправки адресатам
        $templateData = [
            'COMPLAINT_ID'       => (int)$data['complaintId'],
            'COMPLAINT_NUMBER'   => (string)$data['complaintNumber'],
            'CREATOR_ID'         => (int)$data['creatorId'],
            'CREATOR_NAME'       => $creator->getFullName(),
            'EXPERT_ID'          => (int)$data['expertId'],
            'EXPERT_NAME'        => $expert->getFullName(),
            'CLIENT_ID'          => (int)$data['clientId'],
            'CLIENT_NAME'        => $client->getFullName(),
            'CONSUMPTION_ID'     => (int)$data['consumptionId'],
            'CONSUMPTION_NUMBER' => (string)$data['consumptionNumber'],
            'AGREEMENT_NUMBER'   => (string)$data['agreementNumber'],
            'DATE'               => (string)$data['date'],
            'DIFFERENCES'        => $differences,
        ];

        // Если хоть одна переменная для шаблона не задана, то не отправляем уведомления
        foreach ($templateData as $key => $tempData) {
            if (empty($tempData)) {
                throw new Exception("Template Data ({$key}) is empty!", 500);
            }
        }

        // Получаем email сотрудников из настроек.
        //  я так понимаю здесь какая-то выборка сотрудников,
        //которым разрешено отправлять сообщения в зависимости
        // от $resellerId
        $emails = $seller->getEmailsByPermit( 'tsGoodsReturn');

        $emailFrom = Messenger::getResellerEmailFrom();
        $messenger = new Messenger();
        if (!empty($emailFrom) && count($emails) > 0) {
            foreach ($emails as $email) {
             $messenger->toEmail()->send([
                    0 => [
                        // MessageTypes::EMAIL
                        'emailFrom' => $emailFrom,
                        'emailTo'   => $email,
                        'subject'   => __('complaintEmployeeEmailSubject', $templateData, $resellerId),
                        'message'   => __('complaintEmployeeEmailBody', $templateData, $resellerId),
                    ],
                ], $resellerId, NotificationEvents::CHANGE_RETURN_STATUS);
                $result['notificationEmployeeByEmail'] = true;

            }
        }

        // Шлём клиентское уведомление, только если произошла смена статуса
        if ($notificationType === self::TYPE_CHANGE && !empty($data['differences']['to'])) {
            if (!empty($emailFrom) && !empty($client->email)) {
              $messenger->toEmail()->send([
                    0 => [ // MessageTypes::EMAIL
                        'emailFrom' => $emailFrom,
                        'emailTo'   => $client->email,
                        'subject'   => __('complaintClientEmailSubject', $templateData, $resellerId),
                        'message'   => __('complaintClientEmailBody', $templateData, $resellerId),
                    ],
                ], $resellerId, $client->id, NotificationEvents::CHANGE_RETURN_STATUS, (int)$data['differences']['to']);
                $result['notificationClientByEmail'] = true;
            }

            if (!empty($client->mobile)) {
                $res = $messenger->toSms()->send($resellerId, $client->id, NotificationEvents::CHANGE_RETURN_STATUS, (int)$data['differences']['to'], $templateData, $error);
                if ($res) {
                    $result['notificationClientBySms']['isSent'] = true;
                }
                if (!empty($error)) {
                    $result['notificationClientBySms']['message'] = $error;
                }
            }
        }

        return $result;
    }

    private function __(string $string, null $null, int $resellerId)
    {
    }
}