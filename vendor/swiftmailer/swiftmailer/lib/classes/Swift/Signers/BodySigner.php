<?php
 interface Swift_Signers_BodySigner extends Swift_Signer { public function signMessage(Swift_Message $message); public function getAlteredHeaders(); } 