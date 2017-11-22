<?php

class AudiopreviewController extends Zend_Controller_Action
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('show-preview', 'json')
                    ->addActionContext('audio-preview', 'json')
                    ->addActionContext('get-show', 'json')
                    ->addActionContext('playlist-preview', 'json')
                    ->addActionContext('get-playlist', 'json')
                    ->initContext();
    }

    /**
     * Simply sets up the view to play the required audio track.
     *  Gets the parameters from the request and sets them to the view.
     */
    public function audioPreviewAction()
    {
        $CC_CONFIG = Config::getConfig();

        $audioFileID     = $this->_getParam('audioFileID');
        $audioFileArtist = $this->_getParam('audioFileArtist');
        $audioFileTitle  = $this->_getParam('audioFileTitle');
        $type = $this->_getParam('type');

        $baseUrl = Application_Common_OsPath::getBaseDir();

        $this->view->headScript()->appendFile(
            $baseUrl.'js/airtime/audiopreview/preview_jplayer.js?'.$CC_CONFIG['airtime_version'],
            'text/javascript');
        $this->view->headScript()->appendFile(
            $baseUrl.'js/jplayer/jplayer.playlist.min.js?'.$CC_CONFIG['airtime_version'],
            'text/javascript');
        $this->view->headLink()->appendStylesheet(
            $baseUrl.'js/jplayer/skin/jplayer.airtime.audio.preview.css?'.$CC_CONFIG['airtime_version']);
        $this->_helper->layout->setLayout('audioPlayer');

        $logo = Application_Model_Preference::GetStationLogo();
        if ($logo) {
            $this->view->logo = "data:image/png;base64,$logo";
        } else {
            $this->view->logo = $baseUrl."css/images/airtime_logo_jp.png";
        }

        if ($type == "audioclip") {
            $uri   = $baseUrl."api/get-media/file/".$audioFileID;
            $media = Application_Model_StoredFile::RecallById($audioFileID);
            $mime  = $media->getPropelOrm()->getDbMime();
        } elseif ($type == "stream") {
            $webstream = CcWebstreamQuery::create()->findPk($audioFileID);
            $uri       = $webstream->getDbUrl();
            $mime      = $webstream->getDbMime();
        } else {
            throw new Exception("Unknown type for audio preview!.Type=$type");
        }

        $this->view->uri             = $uri;
        $this->view->mime            = $mime;
        $this->view->audioFileID     = $audioFileID;
        // We need to decode artist and title because it gets
        // encoded twice in js
        $this->view->audioFileArtist = htmlspecialchars(urldecode($audioFileArtist));
        $this->view->audioFileTitle  = htmlspecialchars(urldecode($audioFileTitle));
        $this->view->type            = $type;

        $this->_helper->viewRenderer->setRender('audio-preview');
    }

    /**
     * Simply sets up the view to play the required playlist track.
     *  Gets the parameters from the request and sets them to the view.
     */
    public function playlistPreviewAction()
    {
        $CC_CONFIG = Config::getConfig();

        $playlistIndex = $this->_getParam('playlistIndex');
        $playlistID = $this->_getParam('playlistID');

        $baseUrl = Application_Common_OsPath::getBaseDir();

        $this->view->headScript()->appendFile($baseUrl.'js/airtime/audiopreview/preview_jplayer.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/jplayer/jplayer.playlist.min.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headLink()->appendStylesheet($baseUrl.'js/jplayer/skin/jplayer.airtime.audio.preview.css?'.$CC_CONFIG['airtime_version']);
        $this->_helper->layout->setLayout('audioPlayer');

        $logo = Application_Model_Preference::GetStationLogo();
        if ($logo) {
            $this->view->logo = "data:image/png;base64,$logo";
        } else {
            $this->view->logo = $baseUrl."css/images/airtime_logo_jp.png";
        }
        $this->view->playlistIndex= $playlistIndex;
        $this->view->playlistID = $playlistID;

        $this->_helper->viewRenderer->setRender('audio-preview');
    }

    public function blockPreviewAction()
    {
        $CC_CONFIG = Config::getConfig();

        $blockIndex = $this->_getParam('blockIndex');
        $blockId = $this->_getParam('blockId');

        $baseUrl = Application_Common_OsPath::getBaseDir();

        $this->view->headScript()->appendFile($baseUrl.'js/airtime/audiopreview/preview_jplayer.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/jplayer/jplayer.playlist.min.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headLink()->appendStylesheet($baseUrl.'js/jplayer/skin/jplayer.airtime.audio.preview.css?'.$CC_CONFIG['airtime_version']);
        $this->_helper->layout->setLayout('audioPlayer');

        $logo = Application_Model_Preference::GetStationLogo();
        if ($logo) {
            $this->view->logo = "data:image/png;base64,$logo";
        } else {
            $this->view->logo = $baseUrl."css/images/airtime_logo_jp.png";
        }
        $this->view->blockIndex= $blockIndex;
        $this->view->blockId = $blockId;

        $this->_helper->viewRenderer->setRender('audio-preview');
    }
    public function getBlockAction()
    {
        // disable the view and the layout
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $blockId = $this->_getParam('blockId');

        if (!isset($blockId)) {
            return;
        }

        $bl = new Application_Model_Block($blockId);
        $result = array();
        foreach ($bl->getContents(true) as $ele) {
            $result[] = $this->createElementMap($ele);
        }
        $this->_helper->json($result);
    }
    /**
     *Function will load and return the contents of the requested playlist.
     */
    public function getPlaylistAction()
    {
        // disable the view and the layout
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $playlistID = $this->_getParam('playlistID');

        if (!isset($playlistID)) {
            return;
        }

        $pl = new Application_Model_Playlist($playlistID);
        $result = Array();

        foreach ($pl->getContents(true) as $ele) {
            if ($ele['type'] == 2) {
                // if element is a block expand and add
                $bl = new Application_Model_Block($ele['item_id']);
                if ($bl->isStatic()) {
                    foreach ($bl->getContents(true) as $track) {
                        $result[] = $this->createElementMap($track);
                    }
                }
            } else {
                $result[] = $this->createElementMap($ele);
            }
        }
        $this->_helper->json($result);
    }

    private function createElementMap($track)
    {
        $baseUrl = Application_Common_OsPath::getBaseDir();

        $elementMap = array( 'element_title' => isset($track['track_title'])?$track['track_title']:"",
                'element_artist' => isset($track['artist_name'])?$track['artist_name']:"",
                'element_id' => isset($track['id'])?$track['id']:"",
                'element_position' => isset($track['position'])?$track['position']:"",
                'mime' => isset($track['mime'])?$track['mime']:""
            );

        /* If the track type is static we know it must be
         * a track because static blocks can only contain
         * tracks
         */
        if ($track['type'] == 'static') {
            $track['type'] = 0;
        }
        $elementMap['type'] = $track['type'];

        if ($track['type'] == 0) {
            $mime = $track['mime'];
            //type is file
            if (strtolower($mime) === 'audio/mp3') {
                $elementMap['element_mp3'] = $track['item_id'];
            } elseif (strtolower($mime) === 'audio/ogg') {
                $elementMap['element_oga'] = $track['item_id'];
            } elseif (strtolower($mime) === 'audio/vorbis') {
                $elementMap['element_oga'] = $track['item_id'];
            } elseif (strtolower($mime) === 'audio/mp4') {
                $elementMap['element_m4a'] = $track['item_id'];
            } elseif (strtolower($mime) === 'audio/wav') {
                $elementMap['element_wav'] = $track['item_id'];
            } elseif (strtolower($mime) === 'audio/x-wav') {
                $elementMap['element_wav'] = $track['item_id'];
            } elseif (strtolower($mime) === 'audio/x-flac') {
                $elementMap['element_flac'] = $track['item_id'];
            } else {
                throw new Exception("Unknown file type: $mime");
            }

            $elementMap['uri'] = $baseUrl."api/get-media/file/".$track['item_id'];
        } else {
            $elementMap['uri'] = $track['path'];
        }

        return $elementMap;
    }

    /**
     * Simply sets up the view to play the required show track.
     *  Gets the parameters from the request and sets them to the view.
     */
    public function showPreviewAction()
    {
        $CC_CONFIG = Config::getConfig();

        $showID = $this->_getParam('showID');
        $showIndex = $this->_getParam('showIndex');

        $baseUrl = Application_Common_OsPath::getBaseDir();

        $this->view->headScript()->appendFile($baseUrl.'js/airtime/audiopreview/preview_jplayer.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/jplayer/jplayer.playlist.min.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headLink()->appendStylesheet($baseUrl.'js/jplayer/skin/jplayer.airtime.audio.preview.css?'.$CC_CONFIG['airtime_version']);
        $this->_helper->layout->setLayout('audioPlayer');

        $logo = Application_Model_Preference::GetStationLogo();
        if ($logo) {
            $this->view->logo = "data:image/png;base64,$logo";
        } else {
            $this->view->logo = $baseUrl."css/images/airtime_logo_jp.png";
        }

        $this->view->showID = $showID;
        $this->view->showIndex = $showIndex;

        $this->_helper->viewRenderer->setRender('audio-preview');
    }

    /**
     *Function will load and return the contents of the requested show.
     */
    public function getShowAction()
    {
        $baseUrl = Application_Common_OsPath::getBaseDir();
        // disable the view and the layout
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $showID = $this->_getParam('showID');

        if (!isset($showID)) {
            return;
        }

        $showInstance = new Application_Model_ShowInstance($showID);
        $result = array();
        $position = 0;
        foreach ($showInstance->getShowListContent() as $track) {

            $elementMap = array(
                'element_title' => isset($track['track_title']) ? $track['track_title'] : "",
                'element_artist' => isset($track['creator']) ? $track['creator'] : "",
                'element_position' => $position,
                'element_id' => ++$position,
                'mime' => isset($track['mime'])?$track['mime']:""
            );

            $elementMap['type'] = $track['type'];
            if ($track['type'] == 0) {
                $mime = $track['mime'];
                if (strtolower($mime) === 'audio/mp3') {
                    $elementMap['element_mp3'] = $track['item_id'];
                } elseif (strtolower($mime) === 'audio/ogg') {
                    $elementMap['element_oga'] = $track['item_id'];
                } elseif (strtolower($mime) === 'audio/vorbis') {
                    $elementMap['element_oga'] = $track['item_id'];
                } elseif (strtolower($mime) === 'audio/mp4') {
                    $elementMap['element_m4a'] = $track['item_id'];
                } elseif (strtolower($mime) === 'audio/wav') {
                    $elementMap['element_wav'] = $track['item_id'];
                } elseif (strtolower($mime) === 'audio/x-wav') {
                    $elementMap['element_wav'] = $track['item_id'];
                } elseif (strtolower($mime) === 'audio/x-flac') {
                    $elementMap['element_flac'] = $track['item_id'];
                } else {
                    throw new Exception("Unknown file type: $mime");
                }

                $elementMap['uri'] = $baseUrl."api/get-media/file/".$track['item_id'];
            } else {
                $elementMap['uri'] = $track['filepath'];
            }
            $result[] = $elementMap;
        }

        $this->_helper->json($result);

    }
}
