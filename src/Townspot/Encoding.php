<?php

namespace Townspot;

class Encoding {
    /**
     * @var array Video Formats
     */
    protected $formats = array(
        // Mobile
        'mobile'=>array(
            'output'=>'mp4',
            'size'=>'480x320',
            'video_bitrate'=>'1500k',
            'audio_bitrate'=>'128k',
            'audio_sample_rate'=>'44100',
            'audio_channels_number'=>'2',
            'video_codec'=>'libx264',
            'audio_codec'=>'dolby_heaac',
            'logo'=>array(
                'logo_source'=>'sftp://undead:McXHT8g3ieiqPoJTFCNt@54.202.93.248/home/undead/watermark.png',
                'logo_x'=>5,
                'logo_y'=>5,
                'logo_mode'=>1
            )
        ),
        //Standard
        'standard'=>array(
            'output'=>'mp4',
            'size'=>'640x360',
            'video_bitrate'=>'1000k',
            'audio_bitrate'=>'64k',
            'audio_sample_rate'=>'48000',
            'audio_channels_number'=>'2',
            'video_codec'=>'libx264',
            'audio_codec'=>'dolby_aac',
            'logo'=>array(
                'logo_source'=>"sftp://undead:McXHT8g3ieiqPoJTFCNt@54.202.93.248/home/undead/watermark.png",
                'logo_x'=>529,
                'logo_y'=>334,
                'logo_mode'=>1
            )
        ),

        //High Quality
        'high'=>array(
            'output'=>'mp4',
            'size'=>'854x480',
            'video_bitrate'=>'35000k',
            'audio_bitrate'=>'512k',
            'audio_sample_rate'=>'48000',
            'audio_channels_number'=>'2',
            'video_codec'=>'libx264',
            'audio_codec'=>'dolby_aac',
            'logo'=>array(
                'logo_source'=>'sftp://undead:McXHT8g3ieiqPoJTFCNt@54.202.93.248/home/undead/watermark.png',
                'logo_x'=>743,
                'logo_y'=>454,
                'logo_mode'=>1
            )
        )
    );

    protected $serviceLocator;
    const ACCOUNT_ID = '14867';
    const ACCOUNT_KEY = '122a235703a96cb63ff08689398f5d5f';
    const API_URL = 'http://manage.encoding.com/';


    public function __construct($serviceLocator) {
        ini_set('display_errors', true);
        error_reporting(E_ALL);

        $this->config = $serviceLocator->get('Config')['encoding'];
        $this->amqp = $serviceLocator->get('Config')['amqp'];
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * protected function send($xml)
     * Sends the xml request to Encoding.com
     * @param string $xml
     * @return xml response
     */
    protected function send($xml)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_URL);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "xml=" . urlencode($xml));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        return curl_exec($ch);
        /*
                $this->http(
                    self::API_URL,
                    'POST'
                    "xml=".urlencode($xml)
                );
        */
    }

    /**
     * public function addToQueue($video)
     * Sends video to encoding.com to be queued
     * @param int $video_id - ID of video
     * @return void
     */
    public function addToQueue($video_id)
    {
        $mediaMapper = new \Townspot\Media\Mapper($this->serviceLocator);
        $media = $mediaMapper->find($video_id);

        $fileParts = explode('/', $media->getUrl());
        $file = array_pop($fileParts);

        /**
         * Copy the video into the "outbox" folder in encoding.com's FTP section.
         *
         * Change the name of the video to the video ID so we can easily track it
         *
         */

        $req = new \SimpleXMLElement('<?xml version="1.0"?><query></query>');
        $req->addChild('userid', self::ACCOUNT_ID);
        $req->addChild('userkey', self::ACCOUNT_KEY);
        $req->addChild('action', 'AddMedia');
        $req->addChild('source', "sftp://undead:".$this->config['pass']."@".$this->config['host']."/efs/media/raw/".$file);

        $req->addChild('notify', 'http://'.$_SERVER['HTTP_HOST'] .'/encoding/finished');
        $req->addChild('notify_encoding_errors', 'http://'.$_SERVER['HTTP_HOST'] .'/encoding/error');


        foreach($this->formats as $name=>$format) {
            $formatNode = $req->addChild('format');
            // Format fields
            foreach($format as $property => $value)
            {
                if(is_array($value)) {
                    $arrayNode = $formatNode->addChild($property);
                    foreach($value as $arrayProp=>$arrayVal) {
                        $arrayNode->addChild($arrayProp, $arrayVal);
                    }
                }

                elseif ($value !== '')
                    $formatNode->addChild($property, $value);
            }

            $formatNode->addChild('destination', "sftp://undead:".$this->config['pass']."@".$this->config['host']."/home/undead/in/".$video_id.'_'.$name.'.mp4');
        }

        // Sending API request
        $res = $this->send($req->asXML());
        return true;
    }

    /**
     * public function addToQueue($video)
     * Sends video to encoding.com to be queued
     * @param integer $mediaId
     * @return void
     */
    public function getRuntime($mediaId)
    {

        /**
         * Get the location of the video
         */
$req = "<?xml version='1.0'?>
<query>
    <userid>14867</userid>
    <userkey>122a235703a96cb63ff08689398f5d5f</userkey>
    <action>getMediaInfo</action>
    <mediaid>$mediaId</mediaid>
</query>";


        // Sending API request

        $res = $this->send($req);

        preg_match('/<duration>(.+?)</',$res, $matches);
	$duration = $matches[1];

        return $duration;
    }

    /**
     * Finished Action
     *
     * @return void
     */
    public function finished()
    {
	$f = fopen('/tmp/encfin','a');
        $amqp = $this->amqp;
fputs($f,print_r($amqp,1) . "\n");
        if($xml = $_POST['xml']) {
fputs($f,"post\n");
//            fputs($f,prunt_r($response = new SimpleXMLElement($xml),1));
	    preg_match('/<mediaid>(\d+)<\/mediaid><source>.*raw\/(.+?)\.mp4<\/source>/',$xml,$matches);
	    $video_id = $matches[2];
	    $media_id = $matches[1];
            fputs($f,print_r($matches,1));
//fputs($f,"xml\n");
        }
//fputs($f,'2');die;
//        $videoPath = explode('/',$response->source);
//        $videoName = $videoPath[count($videoPath)-1];
//        $videoNameParts = explode('.',$videoName);
//        $video_id = $videoNameParts[0];
fputs($f,'3 '. $video_id );

        $queue  = $amqp['prefix'] . '/encoding.finished';
fputs($f,"$queue\n");
error_reporting(E_ALL & ~E_DEPRECATED);
        $exchange =  'encoding';
        $conn = new \PhpAmqpLib\Connection\AMQPConnection(
            $amqp['host'],
            $amqp['port'],
            $amqp['user'],
            $amqp['pass'],
            $amqp['vhost']
        );
        $ch = $conn->channel();
        $ch->queue_declare($queue, false, true, false, false);
        $ch->exchange_declare($exchange, 'direct', true, true, false);
        $ch->queue_bind($queue, $exchange);

        $msg_body = json_encode(array(
            'id' => $video_id,
        ));
        $msg = new \PhpAmqpLib\Message\AMQPMessage($msg_body, array('content_type' => 'text/plain', 'delivery_mode' => 2));
        $ch->basic_publish($msg, $exchange);
	$mediaMapper = new \Townspot\Media\Mapper($this->serviceLocator);
        $media = $mediaMapper->find($video_id);
        $media->setDuration(
		$this->getRuntime($media_id)
	);

        $mediaMapper->setEntity($media)->save();
        die('done');
    }
}
