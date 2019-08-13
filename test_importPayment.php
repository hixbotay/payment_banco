<?php
header('Content-type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
<SOAP-ENV:Header/>
<SOAP-ENV:Body>
<ns4:importPaymentResponse xmlns:ns4="http://bancoeconomico.ao/economiconet/spfService">
<resultCode>OK</resultCode>
<payment>
<paymentId>12345</paymentId>
<sourceId>A12345</sourceId>
<entityId>12345</entityId>
<amount>1234.56</amount>
<status>ACTIVE</status>
</payment>
</ns4:importPaymentResponse>
</SOAP-ENV:Body>
</SOAP-ENV:Envelope>';
exit;