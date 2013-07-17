<?php



/**
 * Get the IP address used for external internet-facing applications.
 *
 * @author     Dotan Cohen
 * @version    2013-06-09
 *
 * @return string
 */
function get_external_ip_address()
{
	// TODO: Add a fallback to http://httpbin.org/ip

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
 * @param bool $force_string Force the return of a single address as a string, even if more than one address is found
                             True: Always return a string with a single value
                             False: Always return an array
                             Null (empty): Return a string if a single value, array for multiple values
 *
 * @return bool|string|array
 */
function get_user_ip_address($force_string=NULL)
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

	} elseif ( $force_string===TRUE || ( $force_string===NULL && count($ip_addresses)==1 ) ) {
		return $ip_addresses[0];

	} else {
		return $ip_addresses;
	}
}



?>
