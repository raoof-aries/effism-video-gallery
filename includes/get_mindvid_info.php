<?php



class video_data {

    function data_parse($link) {

        $video_data = array();

        $id = "";

        $data = "";

        $url = "";

        $provider = "";

        $thumbnail = "";

        $author = "";

        $duration = "";



        if (strpos($link, 'youtube.com') !== false || strpos($link, 'youtu.be') !== false) {

            if (strpos($link, 'embed') !== false) {

            }

            else {

                //the ID of the YouTube URL: x6qe_kVaBpg

                $id = $this->parse_youtube($link);

                $data = json_decode(file_get_contents("https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v=$id&format=json"), true);

                $url = $data["provider_url"] . $id;

                $embed = "https://www.youtube.com/embed/$id";

                $provider = $data["provider_name"];

                $thumbnail = $data["thumbnail_url"];

                $author = $data["author_name"];

                $duration = $data["duration"];

            }

        }

        else if (strpos($link, 'vimeo.com') !== false) {

            if (strpos($link, 'player.vimeo.com') !== false) 
            {

            }

            else {

                //the ID of the Vimeo URL: 71673549

                $id = $this->parse_vimeo($link);

                $data = json_decode(file_get_contents(" https://vimeo.com/api/oembed.json?url=https://player.vimeo.com/video/$id"), true);



                $url = "https://vimeo.com/" . $id;

                $embed = "https://player.vimeo.com/video/$id";

                $provider = $data["provider_name"];

                $thumbnail = $data["thumbnail_url"];

                $author = $data["author_name"];

                $duration = $data["duration"];

            }

        }



        $video_data["id"] = $id;

        $video_data["url"] = $url;

        $video_data["embed"] = $embed;

        $video_data["provider"] = $provider;

        $video_data["thumbnail"] = $thumbnail;

        $video_data["author"] = $author;

        $video_data["duration"] = $duration;

        

        return $video_data;

    }



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

}



$video_data = NEW video_data();



?>