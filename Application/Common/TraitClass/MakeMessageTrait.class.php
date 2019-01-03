<?php
namespace Common\TraitClass;
trait MakeMessageTrait {
    /*
     * 大连致通跨境OP与电商平台接口统一订单接口
     * 报文格式 json
     */

    public function DaLianZT ($data)
    {
        $orderId = $data['order_sn_id'];
        $orderDate = date('Y-m-dH:i:s',strtotime($data['create_time']));
        $packingMaterial = null;
        $warehouseId = null;
        $tpl = $data['tpl'];
        $orderType = 2;
        $orderStatus = 'S';
        $customsType = 2;
        $electricCode = $data['electricCode'];
        $cbepcomCode = $data['cbepcomCode'];
        $busiMode = 10;
        $customsCode = '0904';
        $ciqbCode = '211920';
        $stationbCode = null;
        $deliveryCode = null;
        $notes = null;
        $freightFcy = $data['freight'];
        $freightFcode = 'CNY';
        $insuredFee =$data['insurefee'];
        $insurCurr = null;
        $insurMark = null;
        $taxFcy = $data['taxFcy'];
        $taxFcode = 'CNY';
        $otherRate = null;
        $otherCurr = null;
        $otherMark = null;
        $otherPayment = null;
        $otherPaymentCurr = null;
        $fCode = null;
        $discount = $data['coupon_deductible'];
        $buyerName = $data['realname'];
        $buyerIdType = 1;
        $buyerIdNumber = $data['idnumber'];
        $buyerTelephone = $data['mobile'];
        $buyerRegNo = null;
        $grossWeight = null;
        $netWeight = null;
        $bakbCode = null;
        $ordExcStatus = null;
        $forSellComp = null;
        $forSellCompName = null;
        $tradeUnitCode = null;
        $tradeUnitName = null;
        $shippernCode = null;
        $shipDate = null;
        $inputDate = null;
        $logNots = null;
        $blno = null;
        $trans = null;
        $transNo = null;
        $changeFlag = null;
        $tradeMode = null;
        $shipperName = null;
        $shipperAddress = null;
        $shipperPhone = null;
        $agentCode = null;
        $agentName = null;
        $payNo = null;
        $payPcomName = null;
        $payCopNo = null;
        $opType = null;
        $payType = null;
        $payStatus = null;
        $payorName = null;
        $activePayComp = null;
        $acturalPaid = $data['actual_amount'];
        $payCurr = null;
        $payNots = null;
        $payDate = null;
        $fromEplat = null;
        $printHeader = null;
        $commonField = null;
        $isStoreStrategy = null;
        $vmiFlag = null;
        $ownerFlag = null;
        $cutMode = null;
        $transMode = null;
        $packNo = null;
        $wrapType = null;
        $sendCity = null;
        $totalValue = null;
        $goodsInfo = null;
        $orderBatchNo = null;
        $reDeclare = null;

        //goodList数组
        $goodList = $data['goodlist'];
        if( is_array($goodList) ) {
            foreach ($goodList as $k => $gooListValue) {
                $goodList[$k]['gnum'] = $k * 1 + 1;
                $goodList[$k]['goodId'] = $gooListValue['itemno'];
                $goodList[$k]['amount'] = $gooListValue['goods_num'];
                $goodList[$k]['price'] = $gooListValue['price_member'];
                $goodList[$k]['goodPrice'] = null;
                $goodList[$k]['copGName'] = null;
                $goodList[$k]['hsCode'] = null;
                $goodList[$k]['codeTs'] = null;
                $goodList[$k]['decTotal'] = null;
                $goodList[$k]['nots'] = null;
                $goodList[$k]['custGoodsNo'] = null;
                $goodList[$k]['ciqGoodsNo'] = $gooListValue['inspprodcbecCode'];
                $goodList[$k]['batchNo'] = null;
                $goodList[$k]['assemCountry'] = null;
                $goodList[$k]['qtyUnit'] = null;
                $goodList[$k]['spec'] = null;
                $goodList[$k]['storeStrategyId'] = null;
                $goodList[$k]['productionTime'] = null;
                $goodList[$k]['expDate'] = null;
                $goodList[$k]['ownerCode'] = null;
                $goodList[$k]['virtualOwner'] = null;
                $goodList[$k]['brand'] = null;
                $goodList[$k]['packageType'] = null;
                $goodList[$k]['qty1'] = null;
                $goodList[$k]['unit1'] = null;
                $goodList[$k]['qty2'] = null;
                $goodList[$k]['unit2'] = null;
                $goodList[$k]['ggrossWt'] = null;
            }
        } else {
            return "Error: goodList is not a array";
        }

        //recipient数组
        $recipient=array();
                $recipient['name'] = $data['realname'];
                $recipient['receiveType'] = 1;
                $recipient['receiveNo'] = $data['idnumber'];
                $recipient['mobilePhone'] = $data['mobile'];
                $recipient['phone'] = null;
                $recipient['country'] = '中国';
                $recipient['province'] = $data['prov'];
                $recipient['city'] = $data['city'];
                $recipient['district'] = $data['dist'];
                $recipient['address'] = $data['prov']. $data['city'].$data['dist'].$data['address'];
                $recipient['postCode'] = null;
                $recipient['totalFavourable'] = 0;
                $recipient['sender'] = null;
                $recipient['receiver'] = null;
                $recipient['congratulations'] = null;
                $recipient['transportDay'] = null;
                $recipient['recipientProvincesCode'] = null;



        $go = array([
            "orderId" => $orderId,
            "orderDate" => $orderDate,
            "packingMaterial" => $packingMaterial,
            "warehouseId" => $warehouseId,
            "tpl" => $tpl,
            "orderType" => $orderType,
            "orderStatus" => $orderStatus,
            "customsType" => $customsType,
            "electricCode" => $electricCode,
            "cbepcomCode" => $cbepcomCode,
            "busiMode" => $busiMode,
            "customsCode" => $customsCode,
            "ciqbCode" => $ciqbCode,
            "stationbCode" => $stationbCode,
            "deliveryCode" => $deliveryCode,
            "notes" => $notes,
            "freightFcy" => $freightFcy,
            "freightFcode" => $freightFcode,
            "insuredFee" => $insuredFee,
            "insurCurr" => $insurCurr,
            "insurMark" => $insurMark,
            "taxFcy" => $taxFcy,
            "taxFcode" => $taxFcode,
            "otherRate" => $otherRate,
            "otherCurr" => $otherCurr,
            "otherMark" => $otherMark,
            "otherPayment" => $otherPayment,
            "otherPaymentCurr" => $otherPaymentCurr,
            "fCode" => $fCode,
            "discount" => $discount,
            "buyerName" => $buyerName,
            "buyerIdType" => $buyerIdType,
            "buyerIdNumber" => $buyerIdNumber,
            "buyerTelephone" => $buyerTelephone,
            "buyerRegNo" => $buyerRegNo,
            "grossWeight" => $grossWeight,
            "netWeight" => $netWeight,
            "bakbCode" => $bakbCode,
            "ordExcStatus" => $ordExcStatus,
            "forSellComp" => $forSellComp,
            "forSellCompName" => $forSellCompName,
            "tradeUnitCode" => $tradeUnitCode,
            "tradeUnitName" => $tradeUnitName,
            "shippernCode" => $shippernCode,
            "shipDate" => $shipDate,
            "inputDate" => $inputDate,
            "logNots" => $logNots,
            "blno" => $blno,
            "trans" => $trans,
            "transNo" => $transNo,
            "changeFlag" => $changeFlag,
            "tradeMode" => $tradeMode,
            "shipperName" => $shipperName,
            "shipperAddress" => $shipperAddress,
            "shipperPhone" => $shipperPhone,
            "agentCode" => $agentCode,
            "agentName" => $agentName,
            "payNo" => $payNo,
            "payPcomName" => $payPcomName,
            "payCopNo" => $payCopNo,
            "opType" => $opType,
            "payType" => $payType,
            "payStatus" => $payStatus,
            "payorName" => $payorName,
            "activePayComp" => $activePayComp,
            "acturalPaid" => $acturalPaid,
            "payCurr" => $payCurr,
            "payNots" => $payNots,
            "payDate" => $payDate,
            "fromEplat" => $fromEplat,
            "printHeader" => $printHeader,
            "commonField" => $commonField,
            "isStoreStrategy" => $isStoreStrategy,
            "vmiFlag" => $vmiFlag,
            "ownerFlag" => $ownerFlag,
            "cutMode" => $cutMode,
            "transMode" => $transMode,
            "packNo" => $packNo,
            "wrapType" => $wrapType,
            "sendCity" => $sendCity,
            "totalValue" => $totalValue,
            "goodsInfo" => $goodsInfo,
            "orderBatchNo" => $orderBatchNo,
            "reDeclare" => $reDeclare,
            "goodList" =>$goodList,
            "recipient" => $recipient
        ]);
        return json_encode($go,320);
    }




    /*
     * 通联支付网络服务股份有限公司技术规范
     * 报文格式 xml
     */
    public function TongLianZF ($data)
    {
        $VERSION = 'v5.6';
        $VISITOR_ID = 'MCT';
        $MCHT_ID = $data['mcht_id'];
        $ORDER_NO = date('YmdHis').mt_rand(111111111,999999999);
        $TRANS_DATETIME = date('YmdHis');
        $CHARSET = '1';
        $SIGN_TYPE = '1';
        $SIGN_MSG = null;
        $CUSTOMS_CODE = 'HG020';
        $PAYMENT_CHANNEL = '2';
        $CUS_ID = $data['mchid'];
        $PAYMENT_DATETIME = $data['pay_time'];
        $MCHT_ORDER_NO = $data['order_sn_id'];
        $PAYMENT_ORDER_NO = $data['paymentorderId'];
        $PAYMENT_AMOUNT = $data['actual_amount']*100;
        $CURRENCY = '156';
        $ESHOP_ENT_CODE = $data['ebpcode'];
        $ESHOP_ENT_NAME = $data['ebpname'];
        $PAYER_NAME = $data['realname'];
        $PAPER_TYPE = '1';
        $PAPER_NUMBER = $data['idnumber'];
        $PAPER_PHONE = $data['mobile'];
        $MEMO = null;
        $SIGN_MSG_Str=
            "<BODY>" .
            "<CUSTOMS_CODE>".$CUSTOMS_CODE."</CUSTOMS_CODE>" .
            "<PAYMENT_CHANNEL>".$PAYMENT_CHANNEL."</PAYMENT_CHANNEL>" .
            "<CUS_ID>".$CUS_ID."</CUS_ID>" .
            "<PAYMENT_DATETIME>".$PAYMENT_DATETIME."</PAYMENT_DATETIME>" .
            "<MCHT_ORDER_NO>".$MCHT_ORDER_NO."</MCHT_ORDER_NO>" .
            "<PAYMENT_ORDER_NO>".$PAYMENT_ORDER_NO."</PAYMENT_ORDER_NO>" .
            "<PAYMENT_AMOUNT>".$PAYMENT_AMOUNT."</PAYMENT_AMOUNT>" .
            "<CURRENCY>".$CURRENCY."</CURRENCY>" .
            "<ESHOP_ENT_CODE>".$ESHOP_ENT_CODE."</ESHOP_ENT_CODE>" .
            "<ESHOP_ENT_NAME>".$ESHOP_ENT_NAME."</ESHOP_ENT_NAME>" .
            "<PAYER_NAME>".$PAYER_NAME."</PAYER_NAME>" .
            "<PAPER_TYPE>".$PAPER_TYPE."</PAPER_TYPE>" .
            "<PAPER_NUMBER>".$PAPER_NUMBER."</PAPER_NUMBER>" .
            "<PAPER_PHONE>".$PAPER_PHONE."</PAPER_PHONE>" .
            "<MEMO>".$MEMO."</MEMO>" .
            "</BODY>" .
            "<key>".$data['pay_key']."</key>" ;
        $SIGN_MSG=md5($SIGN_MSG_Str);


        $go =  "<PAYMENT_INFO>" .
            "<HEAD>" .
            "<VERSION>".$VERSION."</VERSION>" .
            "<VISITOR_ID>".$VISITOR_ID."</VISITOR_ID>" .
            "<MCHT_ID>".$MCHT_ID."</MCHT_ID>" .
            "<ORDER_NO>".$ORDER_NO."</ORDER_NO>" .
            "<TRANS_DATETIME>".$TRANS_DATETIME."</TRANS_DATETIME>" .
            "<CHARSET>".$CHARSET."</CHARSET>" .
            "<SIGN_TYPE>".$SIGN_TYPE."</SIGN_TYPE>" .
            "<SIGN_MSG>".$SIGN_MSG."</SIGN_MSG>" .
            "</HEAD>" .
            "<BODY>" .
            "<CUSTOMS_CODE>".$CUSTOMS_CODE."</CUSTOMS_CODE>" .
            "<PAYMENT_CHANNEL>".$PAYMENT_CHANNEL."</PAYMENT_CHANNEL>" .
            "<CUS_ID>".$CUS_ID."</CUS_ID>" .
            "<PAYMENT_DATETIME>".$PAYMENT_DATETIME."</PAYMENT_DATETIME>" .
            "<MCHT_ORDER_NO>".$MCHT_ORDER_NO."</MCHT_ORDER_NO>" .
            "<PAYMENT_ORDER_NO>".$PAYMENT_ORDER_NO."</PAYMENT_ORDER_NO>" .
            "<PAYMENT_AMOUNT>".$PAYMENT_AMOUNT."</PAYMENT_AMOUNT>" .
            "<CURRENCY>".$CURRENCY."</CURRENCY>" .
            "<ESHOP_ENT_CODE>".$ESHOP_ENT_CODE."</ESHOP_ENT_CODE>" .
            "<ESHOP_ENT_NAME>".$ESHOP_ENT_NAME."</ESHOP_ENT_NAME>" .
            "<PAYER_NAME>".$PAYER_NAME."</PAYER_NAME>" .
            "<PAPER_TYPE>".$PAPER_TYPE."</PAPER_TYPE>" .
            "<PAPER_NUMBER>".$PAPER_NUMBER."</PAPER_NUMBER>" .
            "<PAPER_PHONE>".$PAPER_PHONE."</PAPER_PHONE>" .
            "<MEMO>".$MEMO."</MEMO>" .
            "</BODY>" .
            "</PAYMENT_INFO>";

        $string = "data=".base64_encode($go);
        return $string;
    }

    /*
     * BC跨境电子商务服务系统对外接口
     * 报文格式 xml
     */
    public function BCKuaJing ($data, $apptype)
    {
        $MessageID = 'CBE103'.date('YmdHis').mt_rand(1111,9999);
        $FunctionCode = 'BC';
        $MessageType = 'CBE103';
        $SenderID = $data['senderid'];
        $ReceiverID = '0';
        $SendTime = date('Y-m-dH:i:s');
        $Version = '1.0';
        $V_ORDERTYPE = '1';
        $V_ORDERNO = $data['order_sn_id'];
        $D_ORDERDATE = date('Y-m-d',strtotime($data['create_time'])).'T'.date('H:i:s',strtotime($data['create_time']));
        $V_EBPCODE = $data['ebpcode'];
        $V_EBCCODE = $data['ebccode'];
        $V_CBECOMCODE = $data['inspentcode'];
        $V_CBEPCOMCODE = $data['inspecpcode'];
        $V_INTERNETDOMAINNAME = $data['domain_name'];
        $V_PAYCODE = $data['paycode'];
        $V_PAYNAME = $data['enterprise_name'];
        $V_PAYTRANSACTIONID = $data['paymentorderId'];
        $N_GOODSVALUE = $data['price_sum'] ;
        $N_FREIGHT = $data['freight'];
        $N_DISCOUNT = $data['coupon_deductible'];
        $N_TAXTOTAL = $data['taxtotal'];
        $N_ACTURALPAID = $data['actual_amount'];
        $V_CURRENCY = '142';
        $V_BUYERREGNO = $data['user_name'];
        $V_BUYERNAME = $data['realname'];
        $V_BUYERIDTYPE = '1';
        $V_BUYERIDNUMBER = $data['idnumber'];
        $V_ORDERDOCTEL = $data['mobile'];
        $V_BATCHNUMBERS = null;
        $V_NOTE = null;
        $V_PLATFORM_NO = $data['platform_no'];
        $V_PLATFORM_SHORT = $data['platform_short'];
        $V_PL_BUSINESS_NO = $data['store_id'];
        $V_PL_BUSINESS_SHORT = $data['ebpname'];
        $V_APPTYPE = $data['send_express_status']?'1':'2';
        $N_INSUREFEE = $data['insurefee'];
        $N_WEIGHT = $data['weight'];
        $N_NETWT = $data['netwt'];
        $N_PACKNO = '1';
        $V_ASSURECODE = $data['assurecode'];

        //GoodInfo
        $GoodInfo = $data['goodlist'];
        if (is_array($GoodInfo) ) {
            $GoodInfoXML = null;
            foreach ($GoodInfo as $k => $GoodInfoValue) {
                $GoodInfoXML .= "<GoodInfo>";
                $GoodInfoXML .= "<V_LOGISTICSORDERNO>".$data['express_id']."</V_LOGISTICSORDERNO>";
                $GoodInfoXML .= "<N_GNUM>".($k * 1 + 1)."</N_GNUM>";
                $GoodInfoXML .= "<V_ITEMNO>".$GoodInfoValue['inspprodcbeccode']."</V_ITEMNO>";
                $GoodInfoXML .= "<V_ENTGOODSNO>".$GoodInfoValue['entgoodsno']."</V_ENTGOODSNO>";
                $GoodInfoXML .= "<V_GOODSINFO>".$GoodInfoValue['description']."</V_GOODSINFO>";
                $GoodInfoXML .= "<N_QTY>".$GoodInfoValue['goods_num']."</N_QTY>";
                $GoodInfoXML .= "<N_PRICE>".$GoodInfoValue['price_member']."</N_PRICE>";
                $GoodInfoXML .= "<N_TOTALPRICE>".($GoodInfoValue['price_member'] * $GoodInfoValue['goods_num'])."</N_TOTALPRICE>";
                $GoodInfoXML .= "<V_SELLWEBSITE>"."http://". $GoodInfoValue['internetdomainname'] ."/product/". $GoodInfoValue['goods_id']."/1</V_SELLWEBSITE>";
                $GoodInfoXML .= "<V_CONSIGNEE>".$data['realname']."</V_CONSIGNEE>";
                $GoodInfoXML .= "<V_CONSIGNEE_IDTYPECODE>1</V_CONSIGNEE_IDTYPECODE>";
                $GoodInfoXML .= "<V_RECEIVENO>".$data['IdNumber']."</V_RECEIVENO>";
                $GoodInfoXML .= "<V_CONSIGNEETELEPHONE>".$data['mobile']."</V_CONSIGNEETELEPHONE>";
                $GoodInfoXML .= "<V_CONSIGNEEADDRESS>".$data['address']."</V_CONSIGNEEADDRESS>";
                $GoodInfoXML .= "<V_RECIPIENTCOUNTRY>142</V_RECIPIENTCOUNTRY>";
                $GoodInfoXML .= "<V_RECIPIENTCITYNAME>".$data['city']."</V_RECIPIENTCITYNAME>";
                $GoodInfoXML .= "<V_RECIPIENTPCODE>".$data['city_no']."</V_RECIPIENTPCODE>";
                $GoodInfoXML .= "<V_RECIPIENTCSDNAME>".$data['address']."</V_RECIPIENTCSDNAME>";
                $GoodInfoXML .= "<V_CONSIGNEEDISTRICT></V_CONSIGNEEDISTRICT>";
                $GoodInfoXML .= "<V_SHIPPERNAME>".$data['shipper_name']."</V_SHIPPERNAME>";
                $GoodInfoXML .= "<V_SHIPPERTELEPHONE>".$data['shipper_telephone']."</V_SHIPPERTELEPHONE>";
                $GoodInfoXML .= "<V_SHIPPERADDRESS>". $data['shipper_address']."</V_SHIPPERADDRESS>";
                $GoodInfoXML .= "<V_SHIPPERCITY>".$data['shipper_city']."</V_SHIPPERCITY>";
                $GoodInfoXML .= "<V_SHIPPERPCODE>".$data['shipper_city_no']."</V_SHIPPERPCODE>";
                $GoodInfoXML .= "<V_SHIPPERCSDNAME>".$data['shipper_dist']."</V_SHIPPERCSDNAME>";
                $GoodInfoXML .= "<V_SHIPPERCOUNTRYCODE>".$data['shipper_country_no']."</V_SHIPPERCOUNTRYCODE>";
                $GoodInfoXML .= "<V_LOGISTICSNO>".$data['express_id']."</V_LOGISTICSNO>";
                $GoodInfoXML .= "<V_BILLNO>".$data['billno']."</V_BILLNO>";
                $GoodInfoXML .= "<N_TOTALAMOUNT>".$data['price_sum']."</N_TOTALAMOUNT>";
                $GoodInfoXML .= "<V_WRAPTYPE>".$GoodInfoValue['wraptype']."</V_WRAPTYPE>";
                $GoodInfoXML .= "<V_NOTE2>".$GoodInfoValue['note2']."</V_NOTE2>";
                $GoodInfoXML .= "<N_QTY1>". $GoodInfoValue['qty1']."</N_QTY1>";
                $GoodInfoXML .= "<N_QTY2>".$GoodInfoValue['qty2']."</N_QTY2>";
                $GoodInfoXML .= "<V_BAK1>".$GoodInfoValue['bak1']."</V_BAK1>";
                $GoodInfoXML .= "<V_BAK2>".$GoodInfoValue['bak2']."</V_BAK2>";
                $GoodInfoXML .= "<V_BAK3>".$GoodInfoValue['bak3']."</V_BAK3>";
                $GoodInfoXML .= "<V_BAK4>".$GoodInfoValue['bak4']."</V_BAK4>";
                $GoodInfoXML .= "<V_BAK5>".$GoodInfoValue['bak5']."</V_BAK5>";
                $GoodInfoXML .= "</GoodInfo>";
            }
        } else {
            return "Error: recipient is not a array";
        }

        $go =  "<Manifest>".
            "<Head>".
                "<MessageID>".$MessageID."</MessageID>".
                "<FunctionCode>".$FunctionCode."</FunctionCode>".
                "<MessageType>".$MessageType."</MessageType>".
                "<SenderID>".$SenderID."</SenderID>".
                "<ReceiverID>".$ReceiverID."</ReceiverID>".
                "<SendTime>".$SendTime."</SendTime>".
                "<Version>".$Version."</Version>".
            "</Head>" .
            "<Declaration>".
                "<OrderInfoList>".
                    "<OrderInfo>".
                        "<V_ORDERTYPE>".$V_ORDERTYPE."</V_ORDERTYPE>".
                        "<V_ORDERNO>".$V_ORDERNO."</V_ORDERNO>".
                        "<D_ORDERDATE>".$D_ORDERDATE."</D_ORDERDATE>".
                        "<V_EBPCODE>".$V_EBPCODE."</V_EBPCODE>".
                        "<V_EBCCODE>".$V_EBCCODE."</V_EBCCODE>".
                        "<V_CBECOMCODE>".$V_CBECOMCODE."</V_CBECOMCODE>".
                        "<V_CBEPCOMCODE>".$V_CBEPCOMCODE."</V_CBEPCOMCODE>".
                        "<V_INTERNETDOMAINNAME>".$V_INTERNETDOMAINNAME."</V_INTERNETDOMAINNAME>".
                        "<V_PAYCODE>".$V_PAYCODE."</V_PAYCODE>".
                        "<V_PAYNAME>".$V_PAYNAME."</V_PAYNAME>".
                        "<V_PAYTRANSACTIONID>".$V_PAYTRANSACTIONID."</V_PAYTRANSACTIONID>".
                        "<N_GOODSVALUE>".$N_GOODSVALUE."</N_GOODSVALUE>".
                        "<N_FREIGHT>".$N_FREIGHT."</N_FREIGHT>".
                        "<N_DISCOUNT>".$N_DISCOUNT."</N_DISCOUNT>".
                        "<N_TAXTOTAL>".$N_TAXTOTAL."</N_TAXTOTAL>".
                        "<N_ACTURALPAID>".$N_ACTURALPAID."</N_ACTURALPAID>".
                        "<V_CURRENCY>".$V_CURRENCY."</V_CURRENCY>".
                        "<V_BUYERREGNO>".$V_BUYERREGNO."</V_BUYERREGNO>".
                        "<V_BUYERNAME>".$V_BUYERNAME."</V_BUYERNAME>".
                        "<V_BUYERIDTYPE>".$V_BUYERIDTYPE."</V_BUYERIDTYPE>".
                        "<V_BUYERIDNUMBER>".$V_BUYERIDNUMBER."</V_BUYERIDNUMBER>".
                        "<V_ORDERDOCTEL>".$V_ORDERDOCTEL."</V_ORDERDOCTEL>".
                        "<V_BATCHNUMBERS>".$V_BATCHNUMBERS."</V_BATCHNUMBERS>".
                        "<V_NOTE>".$V_NOTE."</V_NOTE>".
                        "<V_PLATFORM_NO>".$V_PLATFORM_NO."</V_PLATFORM_NO>".
                        "<V_PLATFORM_SHORT>".$V_PLATFORM_SHORT."</V_PL_BUSINESS_NO>".
                        "<V_PL_BUSINESS_NO>".$V_PL_BUSINESS_NO."</V_PL_BUSINESS_NO>".
                        "<V_PL_BUSINESS_SHORT>".$V_PL_BUSINESS_SHORT."</V_PL_BUSINESS_SHORT>".
                        "<V_APPTYPE>".$V_APPTYPE."</V_APPTYPE>".
                        "<N_INSUREFEE>".$N_INSUREFEE."</N_INSUREFEE>".
                        "<N_WEIGHT>".$N_WEIGHT."</N_WEIGHT>".
                        "<N_NETWT>".$N_NETWT."</N_NETWT>".
                        "<N_PACKNO>".$N_PACKNO."</N_PACKNO>".
                        "<V_ASSURECODE>".$V_ASSURECODE."</V_ASSURECODE>".
                        "<GoodInfoList>".
                            $GoodInfoXML.
                        "</GoodInfoList>".
                    "</OrderInfo>".
                " </OrderInfoList>".
            "</Declaration>".
            "</Manifest>";

        return $go;
    }


    /*
     *大连跨境贸易电子商务单一窗口企业数据交换接口
     *报文格式 XML
     */
    public function DaLianKJ ($data,$type)
    {
        $guid = $this->create_guid($data);
        $appType = '1';
        $appTime = date('YmdHis');
        $appStatus = '2';
        $orderType = 'I';
        $orderNo = $data['order_sn_id'];
        $ebpCode = $data['ebpcode'];
        $ebpName = $data['ebpname'];
        $ebcCode = $data['ebccode'];
        $ebcName = $data['ebcname'];
        $goodsValue = $data['price_sum'];
        $freight = $data['freight'];
        $discount = $data['coupon_deductible'];
        $taxTotal = $data['taxtotal'];
        $acturalPaid = $data['actual_amount'];
        $currency = '142';
        $buyerRegNo = $data['user_name'];
        $buyerName = $data['realname'];
        $buyerTelephone = $data['mobile'];
        $buyerIdType = '1';
        $buyerIdNumber = $data['idnumber'];
        $payCode = $data['paycode'];
        $payName = $data['pay_name'];
        $payTransactionId = $data['paymentorderId'];
        $batchNumbers = null;
        $consignee = $data['realname'];
        $consigneeTelephone = $data['mobile'];
        $consigneeAddress = $data['prov'].$data['city'].$data['dist'].$data['address'];
        $consigneeDitrict = null;
        $note = null;

        //OrderList
        $OrderList = $data['goodlist'];
        if (is_array($OrderList) ) {
            $OrderListXML = null;
            foreach ($OrderList as $k => $OrderListValue) {
                $OrderListXML .= "<ceb:OrderList>";
                $OrderListXML .= "<ceb:gnum>".($k * 1 + 1)."</ceb:gnum>";
                $OrderListXML .= "<ceb:itemNo>".$OrderListValue['itemno']."</ceb:itemNo>";
                $OrderListXML .= "<ceb:itemName>".$OrderListValue['title']."</ceb:itemName>";
                $OrderListXML .= "<ceb:gmodel>".$OrderListValue['inspprodspecs']."</ceb:gmodel>";
                $OrderListXML .= "<ceb:itemDescribe>".$OrderListValue['description']."</ceb:itemDescribe>";
                $OrderListXML .= "<ceb:barCode></ceb:barCode>";
                $OrderListXML .= "<ceb:unit>".$OrderListValue['unit1']."</ceb:unit>";
                $OrderListXML .= "<ceb:qty>".$OrderListValue['goods_num']."</ceb:qty>";
                $OrderListXML .= "<ceb:price>".$OrderListValue['price_member']."</ceb:price>";
                $OrderListXML .= "<ceb:totalPrice>".($OrderListValue['goods_num'] * $OrderListValue['price_member'])."</ceb:totalPrice>";
                $OrderListXML .= "<ceb:currency>142</ceb:currency>";
                $OrderListXML .= "<ceb:country>".$OrderListValue['country']."</ceb:country>";
                $OrderListXML .= "<ceb:note>".$OrderListValue['note']."</ceb:note>";
                $OrderListXML .= "</ceb:OrderList>";
            }
        } else {
            return "Error: recipient is not a array";
        }

        $copCode = $data['copcode'];
        $copName = $data['copname'];
        $dxpMode = 'DXP';
        $dxpId = $data['dxpid'];
        $note_2 = null;

        //签名
        $SignedInfo = null;
        $CanonicalizationMethod = null;
        $SignatureMethod = null;
        $Reference = null;
        $Transforms = null;
        $DigestMethod = null;
        $DigestValue = null;
        $SignatureValue = null;
        $KeyInfo = null;
        $KeyName = null;
        $X509Data = null;
        $X509Certificate = null;

        $procTarget = $type;
        $agentCode = $data['agentcode'];
        $inspEntCode = $data['inspcbeCode'];
        $inspEntName = $data['inspentName'];
        $inspEcpCode = $data['inspecpCode'];
        $inspCbeCode = $data['inspcbeCode'];
        $inspCurrUnit = '142';
        $inspBizType = $data['busi_mode']=='BBC'?'1':'2';
        $inspTradeCtryCode = $data['country'];
        $inspOrgCode = $data['insporgcode'];

        //InspOrderList
        $InspOrderList = $data['goodlist'];
        if (is_array($InspOrderList) ) {
            $InspOrderListtXML = null;
            foreach ($InspOrderList as $k => $InspOrderListValue) {
                $InspOrderListtXML .= "<InspOrderList>";
                $InspOrderListtXML .= "<gnum>" . ($k * 1 + 1) . "<gnum>";
                $InspOrderListtXML .= "<inspCurrUnit>142</inspCurrUnit>";
                $InspOrderListtXML .= "<inspOriCtryCode>" . $OrderListValue['country'] . "</inspOriCtryCode>";
                $InspOrderListtXML .= "<inspQtyUnitCode>" . $OrderListValue['unit'] . "</inspQtyUnitCode>";
                $InspOrderListtXML .= "<inspPackNumber>" . $OrderListValue['goods_num'] . "</inspPackNumber>";
                $InspOrderListtXML .= "<inspPackTypeCode>" . $OrderListValue['note2'] . "</inspPackTypeCode>";
                $InspOrderListtXML .= "<inspProdSpecs>" . $OrderListValue['inspprodspecs'] . "</inspProdSpecs>";
                $InspOrderListtXML .= "<inspGrossWeight>" . ($OrderListValue['weight'] * $OrderListValue['goods_num']) . "</inspGrossWeight>";
                $InspOrderListtXML .= "<inspProdCbecCode>" . $OrderListValue['inspprodcbeccode'] . "</inspProdCbecCode>";
                $InspOrderListtXML .= "</InspOrderList>";
            }
        }

        $go = '<?xml version="1.0" encoding="UTF-8"?>'.
            '<ceb:CEB311Message guid="'.$guid.'" version="1.0"  xmlns:ceb="http://www.chinaport.gov.cn/ceb"  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'.
            '<ceb:Order>'.
                '<ceb:OrderHead>'.
                    '<ceb:guid>'.$guid.'</ceb:guid>'.
                    '<ceb:appType>'.$appType.'</ceb:appType>'.
                    '<ceb:appTime>'.$appTime.'</ceb:appTime>'.
                    '<ceb:appStatus>'.$appStatus.'</ceb:appStatus>'.
                    '<ceb:orderType>'.$orderType.'</ceb:orderType>'.
                    '<ceb:orderNo>'.$orderNo.'</ceb:orderNo>'.
                    '<ceb:ebpCode>'.$ebpCode.'</ceb:ebpCode>'.
                    '<ceb:ebpName>'.$ebpName.'</ceb:ebpName>'.
                    '<ceb:ebcCode>'.$ebcCode.'</ceb:ebcCode>'.
                    '<ceb:ebcName>'.$ebcName.'</ceb:ebcName>'.
                    '<ceb:goodsValue>'.$goodsValue.'</ceb:goodsValue>'.
                    '<ceb:freight>'.$freight.'</ceb:freight>'.
                    '<ceb:discount>'.$discount.'</ceb:discount>'.
                    '<ceb:taxTotal>'.$taxTotal.'</ceb:taxTotal>'.
                    '<ceb:acturalPaid>'.$acturalPaid.'</ceb:acturalPaid>'.
                    '<ceb:currency>'.$currency.'</ceb:currency>'.
                    '<ceb:buyerRegNo>'.$buyerRegNo.'</ceb:buyerRegNo>'.
                    '<ceb:buyerName>'.$buyerName.'</ceb:buyerName>'.
                    '<ceb:buyerTelephone>'.$buyerTelephone.'</ceb:buyerTelephone>'.
                    '<ceb:buyerIdType>'.$buyerIdType.'</ceb:buyerIdType>'.
                    '<ceb:buyerIdNumber>'.$buyerIdNumber.'</ceb:buyerIdNumber>'.
                    '<ceb:payCode>'.$payCode.'</ceb:payCode>'.
                    '<ceb:payName>'.$payName.'</ceb:payName>'.
                    '<ceb:payTransactionId>'.$payTransactionId.'</ceb:payTransactionId>'.
                    '<ceb:batchNumbers>'.$batchNumbers.'</ceb:batchNumbers>'.
                    '<ceb:consignee>'.$consignee.'</ceb:consignee>'.
                    '<ceb:consigneeTelephone>'.$consigneeTelephone.'</ceb:consigneeTelephone>'.
                    '<ceb:consigneeAddress>'.$consigneeAddress.'</ceb:consigneeAddress>'.
//                    '<ceb:consigneeDitrict>'.$consigneeDitrict.'</ceb:consigneeDitrict>'.
                    '<ceb:note>'.$note.'</ceb:note>'.
                '</ceb:OrderHead>'.
            $OrderListXML.
            '</ceb:Order>'.
            '<ceb:BaseTransfer>'.
                '<ceb:copCode>'.$copCode.'</ceb:copCode>'.
                '<ceb:copName>'.$copName.'</ceb:copName>'.
                '<ceb:dxpMode>'.$dxpMode.'</ceb:dxpMode>'.
                '<ceb:dxpId>'.$dxpId.'</ceb:dxpId>'.
                '<ceb:note>'.$note_2.'</ceb:note>'.
            '</ceb:BaseTransfer>'.
            '<ceb:ExtendMessage>'.
                '<ceb:name>自定义报文名称</ceb:name>'.
                '<ceb:version>自定义报文版本</ceb:version>'.
                '<ceb:Message>'.
                    '<InspOrder>'.
                    '<BodyMaster>'.
                        '<procTarget>'.$procTarget.'</procTarget>'.
                        '<agentCode>'.$agentCode.'</agentCode>'.
                        '<inspEntCode>'.$inspEntCode.'</inspEntCode>'.
                        '<inspEntName>'.$inspEntName.'</inspEntName>'.
                        '<inspEcpCode>'.$inspEcpCode.'</inspEcpCode>'.
                        '<inspCbeCode>'.$inspCbeCode.'</inspCbeCode>'.
                        '<inspCurrUnit>'.$inspCurrUnit.'</inspCurrUnit>'.
                        '<inspBizType>'.$inspBizType.'</inspBizType>'.
                        '<inspTradeCtryCode>'.$inspTradeCtryCode.'</inspTradeCtryCode>'.
                        '<inspOrgCode>'.$inspOrgCode.'</inspOrgCode>'.
                    '</BodyMaster>'.
            $InspOrderListtXML.
                '</ceb:Message>'.
            '</ceb:ExtendMessage>'.
            '<ds:Signature xmlns:ds="http://www.w3.org/2000/09/xmldsig#">'.
                '<ds:SignedInfo>'.
                    '<ds:CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315">'.$CanonicalizationMethod.'</ds:CanonicalizationMethod>'.
                    '<ds:SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1">'.$SignatureMethod.'</ds:SignatureMethod>'.
                        '<ds:Reference URI="">'.
                            '<ds:Transforms>'.
                                '<ds:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature">'.$Transforms.'</ds:Transform>'.
                            '</ds:Transforms>'.
                            '<ds:DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1">'.$DigestMethod.'</ds:DigestMethod>'.
                            '<ds:DigestValue>'.$DigestValue.'</ds:DigestValue>'.
                        '</ds:Reference>'.
                    '</ds:SignedInfo>'.
                    '<ds:SignatureValue>'.$SignatureValue.'</ds:SignatureValue>'.
                    '<ds:KeyInfo>'.
                        '<ds:KeyName>'.$KeyName.'</ds:KeyName>'.
                        '<ds:X509Data>'.
                            '<ds:X509Certificate>'.$X509Certificate.'</ds:X509Certificate>'.
                        '</ds:X509Data>'.
                    '</ds:KeyInfo>'.
                '</ds:Signature>'.
            '</ceb:CEB311Message>';

        return $go;
    }

    //生产$guid
    public function create_guid($data){
        $string='';
        if($data['busi_mode']==BC)
            $string=$data['csuc_code'].'AI'.substr(uniqid($data['id']),0,12).$data['platform_short'];
        else
            $string=$data['csuc_code'].'BI'.substr(uniqid($data['id']),0,12).$data['platform_short'];
        return $string;
    }

}
