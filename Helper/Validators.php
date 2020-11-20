<?php
 
namespace MailboxValidator\EmailValidator\Helper;
 
class Validators extends \Magento\Framework\App\Helper\AbstractHelper
{
	
	public function mbvsingle( $emailAddress, $api_key )
	{

		try{
			// Now we need to send the data to MBV API Key and return back the result.
			$url = 'https://api.mailboxvalidator.com/v1/validation/single?key=' . str_replace( ' ', '', $api_key ) . '&email=' . str_replace( ' ', '', $emailAddress ) . '&source=magento';

			$results = $this->http( $url );

			if ( $results != false ) {
				// Decode the return json results and return the data.
				$data = json_decode( $results, true );
				
				return $data;
			} else {
				// if connection error, let it pass
				return true;
			}
		}
		catch( Exception $e ) {
			return true;
		}
	}
	
	public function mbvisvalidemail( $api_result )
	{
		if ( $api_result != '' ) {
			if ( $api_result['error_message'] == '' ) {
				if ( $api_result['status'] == 'False' ) {
					return false;
				} else {
					return true;
				}
			} else {
				// If error message occured, let it pass first.
				return true;
			}
		} else {
			// If error message occured, let it pass first.
			return true;
		}
	}

	public function mbvisrole( $api_result )
	{
		if ( $api_result != '' ) {
			if ( $api_result['error_message'] == '' ) {
				if ( $api_result['is_role'] == 'True' ) {
					return true;
				} else {
					return false;
				}
			} else {
				// If error message occured, let it pass first.
				return false;
			}
		} else {
			// If error message occured, let it pass first.
			return false;
		}
	}

	public function mbvisfree( $emailAddress, $api_key )
	{

		try{
			// Now we need to send the data to MBV API Key and return back the result.
			$url = 'https://api.mailboxvalidator.com/v1/email/free?key=' . str_replace( ' ', '', $api_key ) . '&email=' . str_replace( ' ', '', $emailAddress ) . '&source=magento';

			$results = $this->http( $url );

			if ( $results != false ) {
				// Decode the return json results and return the data.
				$data = json_decode( $results, true );
				
				// if ($debug_mode_on_off == true) {
					// file_put_contents ( __DIR__ . '/mbv_plugin_logs.log' , var_export( $data, true ) . PHP_EOL, FILE_APPEND );
				// }

				if ( $data['error_message'] == '' ) {
					if ( $data['is_free'] == 'False' ) {
						return false;
					} else {
						return true;
					}
				} else {
					// If error message occured, let it pass first.
					return false;
				}
			} else {
				// if connection error, let it pass
				return false;
			}
		}
		catch( Exception $e ) {
			return false;
		}
	}


	public function mbvisdisposable( $emailAddress, $api_key )
	{

		try{
			// Now we need to send the data to MBV API Key and return back the result.
			$url = 'https://api.mailboxvalidator.com/v1/email/disposable?key=' . str_replace( ' ', '', $api_key ) . '&email=' . str_replace( ' ', '', $emailAddress ) . '&source=magento';

			$results = $this->http( $url );

			if ( $results != false ) {
				// Decode the return json results and return the data.
				$data = json_decode($results, true);
				
				// if ($debug_mode_on_off == true) {
					// file_put_contents ( __DIR__ . '/mbv_plugin_logs.log' , var_export( $data, true ) . PHP_EOL, FILE_APPEND );
				// }

				if ( $data['error_message'] == '' ) {
					if ( $data['is_disposable'] == 'False' ) {
						return false;
					} else {
						return true;
					}
				} else {
					// If error message occured, let it pass first.
					return false;
				}
			} else {
				// if connection error, let it pass
				return false;
			}
		}
		catch( Exception $e ) {
			return false;
		}

	}
	
	public function http($url) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $result = curl_exec($ch);

        if (!curl_errno($ch))
            return $result;

        curl_close($ch);

        return false;
    }
}