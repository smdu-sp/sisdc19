<?php
require __DIR__ . '/google-api-php-client-2.4.1/vendor/autoload.php';
require_once "config.php";

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient()
{
    $client = new Google_Client();
    $client->setApplicationName('SISDC19');
    $client->setScopes(Google_Service_Sheets::SPREADSHEETS);
    $client->setAuthConfig('./google-api-php-client-2.4.1/credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // Load previously authorized token from a file, if it exists.
    // The file token.json stores the user's access and refresh tokens, and is
    // created automatically when the authorization flow completes for the first
    // time.
    $tokenPath = './google-api-php-client-2.4.1/token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // If there is no previous token or it's expired.
    if ($client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }

    return $client;
}

function atualizaGSheets($nomeTabela, $idPlanilha)
{
	global $link;
	$client = getClient();
	$serviceSheets = new Google_Service_Sheets($client);

	$sql = "SELECT * FROM `".$nomeTabela."`;";
	$dbTable = [];
	// Obtem dados do banco e armazena no array
	$retorno = $link->query($sql);
	if ($retorno->num_rows>0) {
		while ($row = $retorno->fetch_assoc()) {
			array_push($dbTable, $row);
		}
	}

	$sheetValues = [[]];

	// Preenche nomes das colunas
	foreach ($dbTable[0] as $key => $value) {
		array_push($sheetValues[0], $key);
	}
	// Preenche valores da tabela
	foreach ($dbTable as $key => $value) {
		$valCols = array_values($value);
		foreach ($valCols as $vKey => $vVal) {
			// $valCols[$vKey] = json_encode($vVal);
			$valCols[$vKey] = utf8_decode(utf8_encode($vVal));
		}
		array_push($sheetValues, $valCols);
	}

	$updateRange = $nomeTabela;

	$sheetBody = new \Google_Service_Sheets_ValueRange([
		'range' => $updateRange,
		'majorDimension' => 'ROWS',
		'values' => $sheetValues
	]);

	$response = $serviceSheets->spreadsheets_values->update(
		$idPlanilha,
		$updateRange,
		$sheetBody,
		['valueInputOption' => 'RAW']
	);

	return $response;
	
}

foreach ($dbTables as $nomeTabela => $idPlanilha) {
	$retorno = atualizaGSheets($nomeTabela, $idPlanilha);
	if (!$retorno->updatedCells > 0) {
		printf("Erro ao gravar no Google Sheets!\n\n<br><br>");
		var_dump($retorno);
		break;
	}
}

?>