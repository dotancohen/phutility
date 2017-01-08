<?php



/**
 * Get the IP address used for public internet-facing applications.
 *
 * @author     Dotan Cohen
 * @version    2013-06-09
 *
 * @return string
 */
function get_public_ip_address()
{
	// TODO: Add a fallback to http://httpbin.org/ip
	// TODO: Add a fallback to http://169.254.169.254/latest/meta-data/public-ipv4

	$url="simplesniff.com/ip";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	$data = curl_exec($ch);
	curl_close($ch);

	return $data;
}



/**
 * Get the IP address of the client accessing the website
 *
 * @author     Dotan Cohen
 * @version    2013-07-02
 *
 * @param null $return_type 'array', 'single'
 *
 * @return array|bool|mixed
 */
function get_user_ip_address($return_type=NULL)
{
	// Consider: http://stackoverflow.com/questions/4581789/how-do-i-get-user-ip-address-in-django
	// Consider: http://networkengineering.stackexchange.com/questions/2283/how-to-to-determine-if-an-address-is-a-public-ip-address

	$ip_addresses = array();
	$ip_elements = array(
		'HTTP_X_FORWARDED_FOR', 'HTTP_FORWARDED_FOR',
		'HTTP_X_FORWARDED', 'HTTP_FORWARDED',
		'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_CLUSTER_CLIENT_IP',
		'HTTP_X_CLIENT_IP', 'HTTP_CLIENT_IP',
		'REMOTE_ADDR'
	);


	foreach ( $ip_elements as $element ) {
		if(isset($_SERVER[$element])) {
			if ( !is_string($_SERVER[$element]) ) {
				// Log the value somehow, to improve the script!
				continue;
			}
			$address_list = explode(',', $_SERVER[$element]);
			$address_list = array_map('trim', $address_list);
			// Not using array_merge in order to preserve order
			foreach ( $address_list as $x ) {
				$ip_addresses[] = $x;
			}
		}
	}


	if ( count($ip_addresses)==0 ) {
		return FALSE;

	} elseif ( $return_type==='array' ) {
		return $ip_addresses;

	} elseif ( $return_type==='single' || $return_type===NULL ) {
		return $ip_addresses[0];
	}

}


?>
