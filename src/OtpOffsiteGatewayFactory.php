<?php
/**
 * Contains class Capture
 *
 * @package     Konekt\PayumOtp\Request\Api
 * @copyright   Copyright (c) 2016 Storm Storez Srl-D
 * @author      Lajos Fazakas
 * @license     MIT
 * @since       2016-03-15
 * @version     2016-03-15
 */

namespace Konekt\PayumOtp;

use Konekt\PayumOtp\Action\Api\FetchCaptureResultAction;
use Konekt\PayumOtp\Action\Api\InitiateCaptureAction;
use Konekt\PayumOtp\Action\AuthorizeAction;
use Konekt\PayumOtp\Action\CancelAction;
use Konekt\PayumOtp\Action\ConvertPaymentAction;
use Konekt\PayumOtp\Action\CaptureAction;
use Konekt\PayumOtp\Action\NotifyAction;
use Konekt\PayumOtp\Action\RefundAction;
use Konekt\PayumOtp\Action\StatusAction;
use Konekt\PayumOtp\Bridge\OtpSdk4\Api;
use Konekt\PayumOtp\Bridge\OtpSdk4\Configurator;
use Konekt\PayumOtp\Bridge\OtpSdk4\Util\TransactionIdGenerator;
use Konekt\PayumOtp\Request\Api\Capture;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

/**
 * Class OtpOffsiteGatewayFactory
 * 
 * Payum gateway class for OTP offsite payments.
 */
class OtpOffsiteGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => 'otp_hungary_offsite',
            'payum.factory_title' => 'Otp Hungary Offsite',
            'payum.action.capture' => new CaptureAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),

            'payum.action.fetch_capture_result' => new FetchCaptureResultAction(),
            'payum.action.initiate_capture' => new InitiateCaptureAction(),
        ]);

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = array(
                'sandbox' => true,
                'transactionid_prefix' => ''
            );

            $config->defaults($config['payum.default_options']);

            $requiredOptions = [
                'sdk_dir',
                'pos_id'
            ];

            if (true === $config['sandbox']) {
                $config['secret_key'] = __DIR__ . '/../data/#02299991.privKey.pem';
                $config['pos_id'] = '#02299991';
            } else {
                $requiredOptions[] = 'secret_key';
            }

            if ($config['transactionid_prefix']) {
                TransactionIdGenerator::assertPrefixValid($config['transactionid_prefix']);
            }

            $config['payum.required_options'] = $requiredOptions;

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                return new Api(new Configurator((array) $config));
            };
        }
    }
}
