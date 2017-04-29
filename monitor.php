<?php

$urlMiner = "http://xxxxxx.ethosdistro.com/?json=yes";

$ch = curl_init();

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_URL,$urlMiner);

$result=curl_exec($ch);

curl_close($ch);

$dataRigs = json_decode($result, true);
foreach ($dataRigs["rigs"] as $worker) {

	if ($worker["condition"] == "mining") {

		$workerGpuHashes = $worker["miner_hashes"];

		$wrHash = explode(" ", $workerGpuHashes);
		$alert  = 0;

		foreach ($wrHash as $key => $value) {

			if ($value == 0.00) {
				$alert = 1;
			}
		}

		if ($alert == 1) {

			require_once "Mail-1.4.1/Mail.php";
			$worker  = key($dataRigs["rigs"]);
			$from    = '<xxxxxx@xxxxxx.com>';
			$to      = '<xxxxxx@xxxxxx.com>';
			$subject = 'Alerta: Worker '.key($dataRigs["rigs"]);
			$body    = "Tenemos un problema con el Worker ".$worker.", un GPU esta apagado...";

			$headers = array(
				'From'    => $from,
				'To'      => $to,
				'Subject' => $subject
			);

			$smtp = Mail::factory('smtp', array(
			        'host' => 'ssl://smtp.gmail.com',
			        'port' => '465',
			        'auth' => true,
			        'username' => '',
			        'password' => ''
			    ));

			$mail = $smtp->send($to, $headers, $body);

		}
	}
}

?>