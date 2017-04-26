<?php
/**
 * 2007-2015 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Mercado Pago
 *  @copyright Copyright (c) MercadoPago [http://www.mercadopago.com]
 *  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of Mercado Pago
 */

class UtilMercadoPago
{
    public static function logMensagem($mensagem, $nivel)
    {
        $version = UtilMercadoPago::getPrestashopVersion();
        $data_hora = date("F j, Y, g:i a");
        if ($version >= 6) {
            PrestaShopLogger::addLog(
                $mensagem,
                1,
                $nivel,
                null,
                null,
                true
            );
        } else if ($version == 5) {
            Logger::addLog(
                $mensagem,
                1,
                $nivel,
                null,
                null,
                true
            );

        } else {
            error_log($data_hora."===".$mensagem);
        }
    }

    public static function setNamePaymentType($payment_type_id)
    {
        if ($payment_type_id == "ticket") {
            $displayName = "Mercado Pago - ticket";
        } elseif ($payment_type_id == "atm") {
            $displayName = "Mercado Pago - ATM";
        } elseif ($payment_type_id == "credit_card") {
            $displayName = "Mercado Pago - Credit card";
        } elseif ($payment_type_id == "debit_card") {
            $displayName = "Mercado Pago - Debit card";
        } elseif ($payment_type_id == "prepaid_card") {
            $displayName = "Mercado Pago - Prepaid card";
        } else {
            $displayName = "Mercado Pago";
        }
        return $displayName;
    }

    public static function getPrestashopVersion()
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $version = 7;
        } elseif (version_compare(_PS_VERSION_, '1.6.0.1', '>=')) {
            $version = 6;
        } elseif (version_compare(_PS_VERSION_, '1.5.0.1', '>=')) {
            $version = 5;
        } else {
            $version = 4;
        }
        return $version;
    }

    /***
     * Check the requirements of module
     * @return array
     */
    public static function checkRequirements()
    {
        $requirements = array(
            'dimensoes' => '',
            'version' => '',
            'curl' => '',
            'ssl' => ''
            );

        $version = str_replace('.', '', phpversion());

        if ($version < 533) {
            $requirements['version'] = 'negative';
        } else {
            $requirements['version'] = 'positive';
        }

        if (!function_exists('curl_init')) {
            $requirements['curl'] = 'negative';
        } else {
            $requirements['curl'] = 'positive';
        }

        $sql = "SELECT id_product
        FROM "._DB_PREFIX_."product
        WHERE width = 0 OR height = 0 OR depth = 0 OR weight = 0";

        $dados = Db::getInstance()->executeS($sql);

        if ($dados) {
            $requirements['dimensoes'] = 'negative';
        } else {
            $requirements['dimensoes'] = 'positive';
        }

        $requirements['ssl'] = Configuration::get('PS_SSL_ENABLED') == 0 ? "negative" : "positive";

        return $requirements;
    }

}
