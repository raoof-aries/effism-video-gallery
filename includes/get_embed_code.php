<?php

function parse_youtube($link){

    $regexstr = '~
			# Match Youtube link and embed code
			(?:				 				# Group to match embed codes
				(?:<iframe [^>]*src=")?	 	# If iframe match up to first quote of src
				|(?:				 		# Group to match if older embed
					(?:<object .*>)?		# Match opening Object tag
					(?:<param .*</param>)*  # Match all param tags
					(?:<embed [^>]*src=")?  # Match embed tag to the first quote of src
				)?				 			# End older embed code group
			)?				 				# End embed code groups
			(?:				 				# Group youtube url
				https?:\/\/		         	# Either http or https
				(?:[\w]+\.)*		        # Optional subdomains
				(?:               	        # Group host alternatives.
				youtu\.be/      	        # Either youtu.be,
				| youtube\.com		 		# or youtube.com 
				| youtube-nocookie\.com	 	# or youtube-nocookie.com
				)				 			# End Host Group
				(?:\S*[^\w\-\s])?       	# Extra stuff up to VIDEO_ID
				([\w\-]{11})		        # $1: VIDEO_ID is numeric
				[^\s]*			 			# Not a space
			)				 				# End group
			"?				 				# Match end quote if part of src
			(?:[^>]*>)?			 			# Match any extra stuff up to close brace
			(?:				 				# Group to match last embed code
				</iframe>		         	# Match the end of the iframe	
				|</embed></object>	        # or Match the end of the older embed
			)?				 				# End Group of last bit of embed code
			~ix';

    preg_match($regexstr, $link, $matches);

    return $matches[1];

}

function parse_vimeo($link){

    $regexstr = '~
			# Match Vimeo link and embed code
			(?:<iframe [^>]*src=")?		# If iframe match up to first quote of src
			(?:							# Group vimeo url
				https?:\/\/				# Either http or https
				(?:[\w]+\.)*			# Optional subdomains
				vimeo\.com				# Match vimeo.com
				(?:[\/\w]*\/videos?)?	# Optional video sub directory this handles groups links also
				\/						# Slash before Id
				([0-9]+)				# $1: VIDEO_ID is numeric
				[^\s]*					# Not a space
			)							# End group
			"?							# Match end quote if part of src
			(?:[^>]*></iframe>)?		# Match the end of the iframe
			(?:<p>.*</p>)?		        # Match any title information stuff
			~ix';

    preg_match($regexstr, $link, $matches);

    return $matches[1];

}

?>