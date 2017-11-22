<?php


/**
 * Base static class for performing query and update operations on the 'cc_files' table.
 *
 * 
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcFilesPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'airtime';

	/** the table name for this class */
	const TABLE_NAME = 'cc_files';

	/** the related Propel class for this table */
	const OM_CLASS = 'CcFiles';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'airtime.CcFiles';

	/** the related TableMap class for this table */
	const TM_CLASS = 'CcFilesTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 70;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'cc_files.ID';

	/** the column name for the NAME field */
	const NAME = 'cc_files.NAME';

	/** the column name for the MIME field */
	const MIME = 'cc_files.MIME';

	/** the column name for the FTYPE field */
	const FTYPE = 'cc_files.FTYPE';

	/** the column name for the DIRECTORY field */
	const DIRECTORY = 'cc_files.DIRECTORY';

	/** the column name for the FILEPATH field */
	const FILEPATH = 'cc_files.FILEPATH';

	/** the column name for the STATE field */
	const STATE = 'cc_files.STATE';

	/** the column name for the CURRENTLYACCESSING field */
	const CURRENTLYACCESSING = 'cc_files.CURRENTLYACCESSING';

	/** the column name for the EDITEDBY field */
	const EDITEDBY = 'cc_files.EDITEDBY';

	/** the column name for the MTIME field */
	const MTIME = 'cc_files.MTIME';

	/** the column name for the UTIME field */
	const UTIME = 'cc_files.UTIME';

	/** the column name for the LPTIME field */
	const LPTIME = 'cc_files.LPTIME';

	/** the column name for the MD5 field */
	const MD5 = 'cc_files.MD5';

	/** the column name for the TRACK_TITLE field */
	const TRACK_TITLE = 'cc_files.TRACK_TITLE';

	/** the column name for the ARTIST_NAME field */
	const ARTIST_NAME = 'cc_files.ARTIST_NAME';

	/** the column name for the BIT_RATE field */
	const BIT_RATE = 'cc_files.BIT_RATE';

	/** the column name for the SAMPLE_RATE field */
	const SAMPLE_RATE = 'cc_files.SAMPLE_RATE';

	/** the column name for the FORMAT field */
	const FORMAT = 'cc_files.FORMAT';

	/** the column name for the LENGTH field */
	const LENGTH = 'cc_files.LENGTH';

	/** the column name for the ALBUM_TITLE field */
	const ALBUM_TITLE = 'cc_files.ALBUM_TITLE';

	/** the column name for the GENRE field */
	const GENRE = 'cc_files.GENRE';

	/** the column name for the COMMENTS field */
	const COMMENTS = 'cc_files.COMMENTS';

	/** the column name for the YEAR field */
	const YEAR = 'cc_files.YEAR';

	/** the column name for the TRACK_NUMBER field */
	const TRACK_NUMBER = 'cc_files.TRACK_NUMBER';

	/** the column name for the CHANNELS field */
	const CHANNELS = 'cc_files.CHANNELS';

	/** the column name for the URL field */
	const URL = 'cc_files.URL';

	/** the column name for the BPM field */
	const BPM = 'cc_files.BPM';

	/** the column name for the RATING field */
	const RATING = 'cc_files.RATING';

	/** the column name for the ENCODED_BY field */
	const ENCODED_BY = 'cc_files.ENCODED_BY';

	/** the column name for the DISC_NUMBER field */
	const DISC_NUMBER = 'cc_files.DISC_NUMBER';

	/** the column name for the MOOD field */
	const MOOD = 'cc_files.MOOD';

	/** the column name for the LABEL field */
	const LABEL = 'cc_files.LABEL';

	/** the column name for the COMPOSER field */
	const COMPOSER = 'cc_files.COMPOSER';

	/** the column name for the ENCODER field */
	const ENCODER = 'cc_files.ENCODER';

	/** the column name for the CHECKSUM field */
	const CHECKSUM = 'cc_files.CHECKSUM';

	/** the column name for the LYRICS field */
	const LYRICS = 'cc_files.LYRICS';

	/** the column name for the ORCHESTRA field */
	const ORCHESTRA = 'cc_files.ORCHESTRA';

	/** the column name for the CONDUCTOR field */
	const CONDUCTOR = 'cc_files.CONDUCTOR';

	/** the column name for the LYRICIST field */
	const LYRICIST = 'cc_files.LYRICIST';

	/** the column name for the ORIGINAL_LYRICIST field */
	const ORIGINAL_LYRICIST = 'cc_files.ORIGINAL_LYRICIST';

	/** the column name for the RADIO_STATION_NAME field */
	const RADIO_STATION_NAME = 'cc_files.RADIO_STATION_NAME';

	/** the column name for the INFO_URL field */
	const INFO_URL = 'cc_files.INFO_URL';

	/** the column name for the ARTIST_URL field */
	const ARTIST_URL = 'cc_files.ARTIST_URL';

	/** the column name for the AUDIO_SOURCE_URL field */
	const AUDIO_SOURCE_URL = 'cc_files.AUDIO_SOURCE_URL';

	/** the column name for the RADIO_STATION_URL field */
	const RADIO_STATION_URL = 'cc_files.RADIO_STATION_URL';

	/** the column name for the BUY_THIS_URL field */
	const BUY_THIS_URL = 'cc_files.BUY_THIS_URL';

	/** the column name for the ISRC_NUMBER field */
	const ISRC_NUMBER = 'cc_files.ISRC_NUMBER';

	/** the column name for the CATALOG_NUMBER field */
	const CATALOG_NUMBER = 'cc_files.CATALOG_NUMBER';

	/** the column name for the ORIGINAL_ARTIST field */
	const ORIGINAL_ARTIST = 'cc_files.ORIGINAL_ARTIST';

	/** the column name for the COPYRIGHT field */
	const COPYRIGHT = 'cc_files.COPYRIGHT';

	/** the column name for the REPORT_DATETIME field */
	const REPORT_DATETIME = 'cc_files.REPORT_DATETIME';

	/** the column name for the REPORT_LOCATION field */
	const REPORT_LOCATION = 'cc_files.REPORT_LOCATION';

	/** the column name for the REPORT_ORGANIZATION field */
	const REPORT_ORGANIZATION = 'cc_files.REPORT_ORGANIZATION';

	/** the column name for the SUBJECT field */
	const SUBJECT = 'cc_files.SUBJECT';

	/** the column name for the CONTRIBUTOR field */
	const CONTRIBUTOR = 'cc_files.CONTRIBUTOR';

	/** the column name for the LANGUAGE field */
	const LANGUAGE = 'cc_files.LANGUAGE';

	/** the column name for the FILE_EXISTS field */
	const FILE_EXISTS = 'cc_files.FILE_EXISTS';

	/** the column name for the SOUNDCLOUD_ID field */
	const SOUNDCLOUD_ID = 'cc_files.SOUNDCLOUD_ID';

	/** the column name for the SOUNDCLOUD_ERROR_CODE field */
	const SOUNDCLOUD_ERROR_CODE = 'cc_files.SOUNDCLOUD_ERROR_CODE';

	/** the column name for the SOUNDCLOUD_ERROR_MSG field */
	const SOUNDCLOUD_ERROR_MSG = 'cc_files.SOUNDCLOUD_ERROR_MSG';

	/** the column name for the SOUNDCLOUD_LINK_TO_FILE field */
	const SOUNDCLOUD_LINK_TO_FILE = 'cc_files.SOUNDCLOUD_LINK_TO_FILE';

	/** the column name for the SOUNDCLOUD_UPLOAD_TIME field */
	const SOUNDCLOUD_UPLOAD_TIME = 'cc_files.SOUNDCLOUD_UPLOAD_TIME';

	/** the column name for the REPLAY_GAIN field */
	const REPLAY_GAIN = 'cc_files.REPLAY_GAIN';

	/** the column name for the OWNER_ID field */
	const OWNER_ID = 'cc_files.OWNER_ID';

	/** the column name for the CUEIN field */
	const CUEIN = 'cc_files.CUEIN';

	/** the column name for the CUEOUT field */
	const CUEOUT = 'cc_files.CUEOUT';

	/** the column name for the SILAN_CHECK field */
	const SILAN_CHECK = 'cc_files.SILAN_CHECK';

	/** the column name for the HIDDEN field */
	const HIDDEN = 'cc_files.HIDDEN';

	/** the column name for the IS_SCHEDULED field */
	const IS_SCHEDULED = 'cc_files.IS_SCHEDULED';

	/** the column name for the IS_PLAYLIST field */
	const IS_PLAYLIST = 'cc_files.IS_PLAYLIST';

	/**
	 * An identiy map to hold any loaded instances of CcFiles objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array CcFiles[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('DbId', 'DbName', 'DbMime', 'DbFtype', 'DbDirectory', 'DbFilepath', 'DbState', 'DbCurrentlyaccessing', 'DbEditedby', 'DbMtime', 'DbUtime', 'DbLPtime', 'DbMd5', 'DbTrackTitle', 'DbArtistName', 'DbBitRate', 'DbSampleRate', 'DbFormat', 'DbLength', 'DbAlbumTitle', 'DbGenre', 'DbComments', 'DbYear', 'DbTrackNumber', 'DbChannels', 'DbUrl', 'DbBpm', 'DbRating', 'DbEncodedBy', 'DbDiscNumber', 'DbMood', 'DbLabel', 'DbComposer', 'DbEncoder', 'DbChecksum', 'DbLyrics', 'DbOrchestra', 'DbConductor', 'DbLyricist', 'DbOriginalLyricist', 'DbRadioStationName', 'DbInfoUrl', 'DbArtistUrl', 'DbAudioSourceUrl', 'DbRadioStationUrl', 'DbBuyThisUrl', 'DbIsrcNumber', 'DbCatalogNumber', 'DbOriginalArtist', 'DbCopyright', 'DbReportDatetime', 'DbReportLocation', 'DbReportOrganization', 'DbSubject', 'DbContributor', 'DbLanguage', 'DbFileExists', 'DbSoundcloudId', 'DbSoundcloudErrorCode', 'DbSoundcloudErrorMsg', 'DbSoundcloudLinkToFile', 'DbSoundCloundUploadTime', 'DbReplayGain', 'DbOwnerId', 'DbCuein', 'DbCueout', 'DbSilanCheck', 'DbHidden', 'DbIsScheduled', 'DbIsPlaylist', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('dbId', 'dbName', 'dbMime', 'dbFtype', 'dbDirectory', 'dbFilepath', 'dbState', 'dbCurrentlyaccessing', 'dbEditedby', 'dbMtime', 'dbUtime', 'dbLPtime', 'dbMd5', 'dbTrackTitle', 'dbArtistName', 'dbBitRate', 'dbSampleRate', 'dbFormat', 'dbLength', 'dbAlbumTitle', 'dbGenre', 'dbComments', 'dbYear', 'dbTrackNumber', 'dbChannels', 'dbUrl', 'dbBpm', 'dbRating', 'dbEncodedBy', 'dbDiscNumber', 'dbMood', 'dbLabel', 'dbComposer', 'dbEncoder', 'dbChecksum', 'dbLyrics', 'dbOrchestra', 'dbConductor', 'dbLyricist', 'dbOriginalLyricist', 'dbRadioStationName', 'dbInfoUrl', 'dbArtistUrl', 'dbAudioSourceUrl', 'dbRadioStationUrl', 'dbBuyThisUrl', 'dbIsrcNumber', 'dbCatalogNumber', 'dbOriginalArtist', 'dbCopyright', 'dbReportDatetime', 'dbReportLocation', 'dbReportOrganization', 'dbSubject', 'dbContributor', 'dbLanguage', 'dbFileExists', 'dbSoundcloudId', 'dbSoundcloudErrorCode', 'dbSoundcloudErrorMsg', 'dbSoundcloudLinkToFile', 'dbSoundCloundUploadTime', 'dbReplayGain', 'dbOwnerId', 'dbCuein', 'dbCueout', 'dbSilanCheck', 'dbHidden', 'dbIsScheduled', 'dbIsPlaylist', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::NAME, self::MIME, self::FTYPE, self::DIRECTORY, self::FILEPATH, self::STATE, self::CURRENTLYACCESSING, self::EDITEDBY, self::MTIME, self::UTIME, self::LPTIME, self::MD5, self::TRACK_TITLE, self::ARTIST_NAME, self::BIT_RATE, self::SAMPLE_RATE, self::FORMAT, self::LENGTH, self::ALBUM_TITLE, self::GENRE, self::COMMENTS, self::YEAR, self::TRACK_NUMBER, self::CHANNELS, self::URL, self::BPM, self::RATING, self::ENCODED_BY, self::DISC_NUMBER, self::MOOD, self::LABEL, self::COMPOSER, self::ENCODER, self::CHECKSUM, self::LYRICS, self::ORCHESTRA, self::CONDUCTOR, self::LYRICIST, self::ORIGINAL_LYRICIST, self::RADIO_STATION_NAME, self::INFO_URL, self::ARTIST_URL, self::AUDIO_SOURCE_URL, self::RADIO_STATION_URL, self::BUY_THIS_URL, self::ISRC_NUMBER, self::CATALOG_NUMBER, self::ORIGINAL_ARTIST, self::COPYRIGHT, self::REPORT_DATETIME, self::REPORT_LOCATION, self::REPORT_ORGANIZATION, self::SUBJECT, self::CONTRIBUTOR, self::LANGUAGE, self::FILE_EXISTS, self::SOUNDCLOUD_ID, self::SOUNDCLOUD_ERROR_CODE, self::SOUNDCLOUD_ERROR_MSG, self::SOUNDCLOUD_LINK_TO_FILE, self::SOUNDCLOUD_UPLOAD_TIME, self::REPLAY_GAIN, self::OWNER_ID, self::CUEIN, self::CUEOUT, self::SILAN_CHECK, self::HIDDEN, self::IS_SCHEDULED, self::IS_PLAYLIST, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID', 'NAME', 'MIME', 'FTYPE', 'DIRECTORY', 'FILEPATH', 'STATE', 'CURRENTLYACCESSING', 'EDITEDBY', 'MTIME', 'UTIME', 'LPTIME', 'MD5', 'TRACK_TITLE', 'ARTIST_NAME', 'BIT_RATE', 'SAMPLE_RATE', 'FORMAT', 'LENGTH', 'ALBUM_TITLE', 'GENRE', 'COMMENTS', 'YEAR', 'TRACK_NUMBER', 'CHANNELS', 'URL', 'BPM', 'RATING', 'ENCODED_BY', 'DISC_NUMBER', 'MOOD', 'LABEL', 'COMPOSER', 'ENCODER', 'CHECKSUM', 'LYRICS', 'ORCHESTRA', 'CONDUCTOR', 'LYRICIST', 'ORIGINAL_LYRICIST', 'RADIO_STATION_NAME', 'INFO_URL', 'ARTIST_URL', 'AUDIO_SOURCE_URL', 'RADIO_STATION_URL', 'BUY_THIS_URL', 'ISRC_NUMBER', 'CATALOG_NUMBER', 'ORIGINAL_ARTIST', 'COPYRIGHT', 'REPORT_DATETIME', 'REPORT_LOCATION', 'REPORT_ORGANIZATION', 'SUBJECT', 'CONTRIBUTOR', 'LANGUAGE', 'FILE_EXISTS', 'SOUNDCLOUD_ID', 'SOUNDCLOUD_ERROR_CODE', 'SOUNDCLOUD_ERROR_MSG', 'SOUNDCLOUD_LINK_TO_FILE', 'SOUNDCLOUD_UPLOAD_TIME', 'REPLAY_GAIN', 'OWNER_ID', 'CUEIN', 'CUEOUT', 'SILAN_CHECK', 'HIDDEN', 'IS_SCHEDULED', 'IS_PLAYLIST', ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'name', 'mime', 'ftype', 'directory', 'filepath', 'state', 'currentlyaccessing', 'editedby', 'mtime', 'utime', 'lptime', 'md5', 'track_title', 'artist_name', 'bit_rate', 'sample_rate', 'format', 'length', 'album_title', 'genre', 'comments', 'year', 'track_number', 'channels', 'url', 'bpm', 'rating', 'encoded_by', 'disc_number', 'mood', 'label', 'composer', 'encoder', 'checksum', 'lyrics', 'orchestra', 'conductor', 'lyricist', 'original_lyricist', 'radio_station_name', 'info_url', 'artist_url', 'audio_source_url', 'radio_station_url', 'buy_this_url', 'isrc_number', 'catalog_number', 'original_artist', 'copyright', 'report_datetime', 'report_location', 'report_organization', 'subject', 'contributor', 'language', 'file_exists', 'soundcloud_id', 'soundcloud_error_code', 'soundcloud_error_msg', 'soundcloud_link_to_file', 'soundcloud_upload_time', 'replay_gain', 'owner_id', 'cuein', 'cueout', 'silan_check', 'hidden', 'is_scheduled', 'is_playlist', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('DbId' => 0, 'DbName' => 1, 'DbMime' => 2, 'DbFtype' => 3, 'DbDirectory' => 4, 'DbFilepath' => 5, 'DbState' => 6, 'DbCurrentlyaccessing' => 7, 'DbEditedby' => 8, 'DbMtime' => 9, 'DbUtime' => 10, 'DbLPtime' => 11, 'DbMd5' => 12, 'DbTrackTitle' => 13, 'DbArtistName' => 14, 'DbBitRate' => 15, 'DbSampleRate' => 16, 'DbFormat' => 17, 'DbLength' => 18, 'DbAlbumTitle' => 19, 'DbGenre' => 20, 'DbComments' => 21, 'DbYear' => 22, 'DbTrackNumber' => 23, 'DbChannels' => 24, 'DbUrl' => 25, 'DbBpm' => 26, 'DbRating' => 27, 'DbEncodedBy' => 28, 'DbDiscNumber' => 29, 'DbMood' => 30, 'DbLabel' => 31, 'DbComposer' => 32, 'DbEncoder' => 33, 'DbChecksum' => 34, 'DbLyrics' => 35, 'DbOrchestra' => 36, 'DbConductor' => 37, 'DbLyricist' => 38, 'DbOriginalLyricist' => 39, 'DbRadioStationName' => 40, 'DbInfoUrl' => 41, 'DbArtistUrl' => 42, 'DbAudioSourceUrl' => 43, 'DbRadioStationUrl' => 44, 'DbBuyThisUrl' => 45, 'DbIsrcNumber' => 46, 'DbCatalogNumber' => 47, 'DbOriginalArtist' => 48, 'DbCopyright' => 49, 'DbReportDatetime' => 50, 'DbReportLocation' => 51, 'DbReportOrganization' => 52, 'DbSubject' => 53, 'DbContributor' => 54, 'DbLanguage' => 55, 'DbFileExists' => 56, 'DbSoundcloudId' => 57, 'DbSoundcloudErrorCode' => 58, 'DbSoundcloudErrorMsg' => 59, 'DbSoundcloudLinkToFile' => 60, 'DbSoundCloundUploadTime' => 61, 'DbReplayGain' => 62, 'DbOwnerId' => 63, 'DbCuein' => 64, 'DbCueout' => 65, 'DbSilanCheck' => 66, 'DbHidden' => 67, 'DbIsScheduled' => 68, 'DbIsPlaylist' => 69, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('dbId' => 0, 'dbName' => 1, 'dbMime' => 2, 'dbFtype' => 3, 'dbDirectory' => 4, 'dbFilepath' => 5, 'dbState' => 6, 'dbCurrentlyaccessing' => 7, 'dbEditedby' => 8, 'dbMtime' => 9, 'dbUtime' => 10, 'dbLPtime' => 11, 'dbMd5' => 12, 'dbTrackTitle' => 13, 'dbArtistName' => 14, 'dbBitRate' => 15, 'dbSampleRate' => 16, 'dbFormat' => 17, 'dbLength' => 18, 'dbAlbumTitle' => 19, 'dbGenre' => 20, 'dbComments' => 21, 'dbYear' => 22, 'dbTrackNumber' => 23, 'dbChannels' => 24, 'dbUrl' => 25, 'dbBpm' => 26, 'dbRating' => 27, 'dbEncodedBy' => 28, 'dbDiscNumber' => 29, 'dbMood' => 30, 'dbLabel' => 31, 'dbComposer' => 32, 'dbEncoder' => 33, 'dbChecksum' => 34, 'dbLyrics' => 35, 'dbOrchestra' => 36, 'dbConductor' => 37, 'dbLyricist' => 38, 'dbOriginalLyricist' => 39, 'dbRadioStationName' => 40, 'dbInfoUrl' => 41, 'dbArtistUrl' => 42, 'dbAudioSourceUrl' => 43, 'dbRadioStationUrl' => 44, 'dbBuyThisUrl' => 45, 'dbIsrcNumber' => 46, 'dbCatalogNumber' => 47, 'dbOriginalArtist' => 48, 'dbCopyright' => 49, 'dbReportDatetime' => 50, 'dbReportLocation' => 51, 'dbReportOrganization' => 52, 'dbSubject' => 53, 'dbContributor' => 54, 'dbLanguage' => 55, 'dbFileExists' => 56, 'dbSoundcloudId' => 57, 'dbSoundcloudErrorCode' => 58, 'dbSoundcloudErrorMsg' => 59, 'dbSoundcloudLinkToFile' => 60, 'dbSoundCloundUploadTime' => 61, 'dbReplayGain' => 62, 'dbOwnerId' => 63, 'dbCuein' => 64, 'dbCueout' => 65, 'dbSilanCheck' => 66, 'dbHidden' => 67, 'dbIsScheduled' => 68, 'dbIsPlaylist' => 69, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::NAME => 1, self::MIME => 2, self::FTYPE => 3, self::DIRECTORY => 4, self::FILEPATH => 5, self::STATE => 6, self::CURRENTLYACCESSING => 7, self::EDITEDBY => 8, self::MTIME => 9, self::UTIME => 10, self::LPTIME => 11, self::MD5 => 12, self::TRACK_TITLE => 13, self::ARTIST_NAME => 14, self::BIT_RATE => 15, self::SAMPLE_RATE => 16, self::FORMAT => 17, self::LENGTH => 18, self::ALBUM_TITLE => 19, self::GENRE => 20, self::COMMENTS => 21, self::YEAR => 22, self::TRACK_NUMBER => 23, self::CHANNELS => 24, self::URL => 25, self::BPM => 26, self::RATING => 27, self::ENCODED_BY => 28, self::DISC_NUMBER => 29, self::MOOD => 30, self::LABEL => 31, self::COMPOSER => 32, self::ENCODER => 33, self::CHECKSUM => 34, self::LYRICS => 35, self::ORCHESTRA => 36, self::CONDUCTOR => 37, self::LYRICIST => 38, self::ORIGINAL_LYRICIST => 39, self::RADIO_STATION_NAME => 40, self::INFO_URL => 41, self::ARTIST_URL => 42, self::AUDIO_SOURCE_URL => 43, self::RADIO_STATION_URL => 44, self::BUY_THIS_URL => 45, self::ISRC_NUMBER => 46, self::CATALOG_NUMBER => 47, self::ORIGINAL_ARTIST => 48, self::COPYRIGHT => 49, self::REPORT_DATETIME => 50, self::REPORT_LOCATION => 51, self::REPORT_ORGANIZATION => 52, self::SUBJECT => 53, self::CONTRIBUTOR => 54, self::LANGUAGE => 55, self::FILE_EXISTS => 56, self::SOUNDCLOUD_ID => 57, self::SOUNDCLOUD_ERROR_CODE => 58, self::SOUNDCLOUD_ERROR_MSG => 59, self::SOUNDCLOUD_LINK_TO_FILE => 60, self::SOUNDCLOUD_UPLOAD_TIME => 61, self::REPLAY_GAIN => 62, self::OWNER_ID => 63, self::CUEIN => 64, self::CUEOUT => 65, self::SILAN_CHECK => 66, self::HIDDEN => 67, self::IS_SCHEDULED => 68, self::IS_PLAYLIST => 69, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'NAME' => 1, 'MIME' => 2, 'FTYPE' => 3, 'DIRECTORY' => 4, 'FILEPATH' => 5, 'STATE' => 6, 'CURRENTLYACCESSING' => 7, 'EDITEDBY' => 8, 'MTIME' => 9, 'UTIME' => 10, 'LPTIME' => 11, 'MD5' => 12, 'TRACK_TITLE' => 13, 'ARTIST_NAME' => 14, 'BIT_RATE' => 15, 'SAMPLE_RATE' => 16, 'FORMAT' => 17, 'LENGTH' => 18, 'ALBUM_TITLE' => 19, 'GENRE' => 20, 'COMMENTS' => 21, 'YEAR' => 22, 'TRACK_NUMBER' => 23, 'CHANNELS' => 24, 'URL' => 25, 'BPM' => 26, 'RATING' => 27, 'ENCODED_BY' => 28, 'DISC_NUMBER' => 29, 'MOOD' => 30, 'LABEL' => 31, 'COMPOSER' => 32, 'ENCODER' => 33, 'CHECKSUM' => 34, 'LYRICS' => 35, 'ORCHESTRA' => 36, 'CONDUCTOR' => 37, 'LYRICIST' => 38, 'ORIGINAL_LYRICIST' => 39, 'RADIO_STATION_NAME' => 40, 'INFO_URL' => 41, 'ARTIST_URL' => 42, 'AUDIO_SOURCE_URL' => 43, 'RADIO_STATION_URL' => 44, 'BUY_THIS_URL' => 45, 'ISRC_NUMBER' => 46, 'CATALOG_NUMBER' => 47, 'ORIGINAL_ARTIST' => 48, 'COPYRIGHT' => 49, 'REPORT_DATETIME' => 50, 'REPORT_LOCATION' => 51, 'REPORT_ORGANIZATION' => 52, 'SUBJECT' => 53, 'CONTRIBUTOR' => 54, 'LANGUAGE' => 55, 'FILE_EXISTS' => 56, 'SOUNDCLOUD_ID' => 57, 'SOUNDCLOUD_ERROR_CODE' => 58, 'SOUNDCLOUD_ERROR_MSG' => 59, 'SOUNDCLOUD_LINK_TO_FILE' => 60, 'SOUNDCLOUD_UPLOAD_TIME' => 61, 'REPLAY_GAIN' => 62, 'OWNER_ID' => 63, 'CUEIN' => 64, 'CUEOUT' => 65, 'SILAN_CHECK' => 66, 'HIDDEN' => 67, 'IS_SCHEDULED' => 68, 'IS_PLAYLIST' => 69, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'name' => 1, 'mime' => 2, 'ftype' => 3, 'directory' => 4, 'filepath' => 5, 'state' => 6, 'currentlyaccessing' => 7, 'editedby' => 8, 'mtime' => 9, 'utime' => 10, 'lptime' => 11, 'md5' => 12, 'track_title' => 13, 'artist_name' => 14, 'bit_rate' => 15, 'sample_rate' => 16, 'format' => 17, 'length' => 18, 'album_title' => 19, 'genre' => 20, 'comments' => 21, 'year' => 22, 'track_number' => 23, 'channels' => 24, 'url' => 25, 'bpm' => 26, 'rating' => 27, 'encoded_by' => 28, 'disc_number' => 29, 'mood' => 30, 'label' => 31, 'composer' => 32, 'encoder' => 33, 'checksum' => 34, 'lyrics' => 35, 'orchestra' => 36, 'conductor' => 37, 'lyricist' => 38, 'original_lyricist' => 39, 'radio_station_name' => 40, 'info_url' => 41, 'artist_url' => 42, 'audio_source_url' => 43, 'radio_station_url' => 44, 'buy_this_url' => 45, 'isrc_number' => 46, 'catalog_number' => 47, 'original_artist' => 48, 'copyright' => 49, 'report_datetime' => 50, 'report_location' => 51, 'report_organization' => 52, 'subject' => 53, 'contributor' => 54, 'language' => 55, 'file_exists' => 56, 'soundcloud_id' => 57, 'soundcloud_error_code' => 58, 'soundcloud_error_msg' => 59, 'soundcloud_link_to_file' => 60, 'soundcloud_upload_time' => 61, 'replay_gain' => 62, 'owner_id' => 63, 'cuein' => 64, 'cueout' => 65, 'silan_check' => 66, 'hidden' => 67, 'is_scheduled' => 68, 'is_playlist' => 69, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, )
	);

	/**
	 * Translates a fieldname to another type
	 *
	 * @param      string $name field name
	 * @param      string $fromType One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                         BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @param      string $toType   One of the class type constants
	 * @return     string translated name of the field.
	 * @throws     PropelException - if the specified name could not be found in the fieldname mappings.
	 */
	static public function translateFieldName($name, $fromType, $toType)
	{
		$toNames = self::getFieldNames($toType);
		$key = isset(self::$fieldKeys[$fromType][$name]) ? self::$fieldKeys[$fromType][$name] : null;
		if ($key === null) {
			throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(self::$fieldKeys[$fromType], true));
		}
		return $toNames[$key];
	}

	/**
	 * Returns an array of field names.
	 *
	 * @param      string $type The type of fieldnames to return:
	 *                      One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                      BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     array A list of field names
	 */

	static public function getFieldNames($type = BasePeer::TYPE_PHPNAME)
	{
		if (!array_key_exists($type, self::$fieldNames)) {
			throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
		}
		return self::$fieldNames[$type];
	}

	/**
	 * Convenience method which changes table.column to alias.column.
	 *
	 * Using this method you can maintain SQL abstraction while using column aliases.
	 * <code>
	 *		$c->addAlias("alias1", TablePeer::TABLE_NAME);
	 *		$c->addJoin(TablePeer::alias("alias1", TablePeer::PRIMARY_KEY_COLUMN), TablePeer::PRIMARY_KEY_COLUMN);
	 * </code>
	 * @param      string $alias The alias for the current table.
	 * @param      string $column The column name for current table. (i.e. CcFilesPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(CcFilesPeer::TABLE_NAME.'.', $alias.'.', $column);
	}

	/**
	 * Add all the columns needed to create a new object.
	 *
	 * Note: any columns that were marked with lazyLoad="true" in the
	 * XML schema will not be added to the select list and only loaded
	 * on demand.
	 *
	 * @param      Criteria $criteria object containing the columns to add.
	 * @param      string   $alias    optional table alias
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function addSelectColumns(Criteria $criteria, $alias = null)
	{
		if (null === $alias) {
			$criteria->addSelectColumn(CcFilesPeer::ID);
			$criteria->addSelectColumn(CcFilesPeer::NAME);
			$criteria->addSelectColumn(CcFilesPeer::MIME);
			$criteria->addSelectColumn(CcFilesPeer::FTYPE);
			$criteria->addSelectColumn(CcFilesPeer::DIRECTORY);
			$criteria->addSelectColumn(CcFilesPeer::FILEPATH);
			$criteria->addSelectColumn(CcFilesPeer::STATE);
			$criteria->addSelectColumn(CcFilesPeer::CURRENTLYACCESSING);
			$criteria->addSelectColumn(CcFilesPeer::EDITEDBY);
			$criteria->addSelectColumn(CcFilesPeer::MTIME);
			$criteria->addSelectColumn(CcFilesPeer::UTIME);
			$criteria->addSelectColumn(CcFilesPeer::LPTIME);
			$criteria->addSelectColumn(CcFilesPeer::MD5);
			$criteria->addSelectColumn(CcFilesPeer::TRACK_TITLE);
			$criteria->addSelectColumn(CcFilesPeer::ARTIST_NAME);
			$criteria->addSelectColumn(CcFilesPeer::BIT_RATE);
			$criteria->addSelectColumn(CcFilesPeer::SAMPLE_RATE);
			$criteria->addSelectColumn(CcFilesPeer::FORMAT);
			$criteria->addSelectColumn(CcFilesPeer::LENGTH);
			$criteria->addSelectColumn(CcFilesPeer::ALBUM_TITLE);
			$criteria->addSelectColumn(CcFilesPeer::GENRE);
			$criteria->addSelectColumn(CcFilesPeer::COMMENTS);
			$criteria->addSelectColumn(CcFilesPeer::YEAR);
			$criteria->addSelectColumn(CcFilesPeer::TRACK_NUMBER);
			$criteria->addSelectColumn(CcFilesPeer::CHANNELS);
			$criteria->addSelectColumn(CcFilesPeer::URL);
			$criteria->addSelectColumn(CcFilesPeer::BPM);
			$criteria->addSelectColumn(CcFilesPeer::RATING);
			$criteria->addSelectColumn(CcFilesPeer::ENCODED_BY);
			$criteria->addSelectColumn(CcFilesPeer::DISC_NUMBER);
			$criteria->addSelectColumn(CcFilesPeer::MOOD);
			$criteria->addSelectColumn(CcFilesPeer::LABEL);
			$criteria->addSelectColumn(CcFilesPeer::COMPOSER);
			$criteria->addSelectColumn(CcFilesPeer::ENCODER);
			$criteria->addSelectColumn(CcFilesPeer::CHECKSUM);
			$criteria->addSelectColumn(CcFilesPeer::LYRICS);
			$criteria->addSelectColumn(CcFilesPeer::ORCHESTRA);
			$criteria->addSelectColumn(CcFilesPeer::CONDUCTOR);
			$criteria->addSelectColumn(CcFilesPeer::LYRICIST);
			$criteria->addSelectColumn(CcFilesPeer::ORIGINAL_LYRICIST);
			$criteria->addSelectColumn(CcFilesPeer::RADIO_STATION_NAME);
			$criteria->addSelectColumn(CcFilesPeer::INFO_URL);
			$criteria->addSelectColumn(CcFilesPeer::ARTIST_URL);
			$criteria->addSelectColumn(CcFilesPeer::AUDIO_SOURCE_URL);
			$criteria->addSelectColumn(CcFilesPeer::RADIO_STATION_URL);
			$criteria->addSelectColumn(CcFilesPeer::BUY_THIS_URL);
			$criteria->addSelectColumn(CcFilesPeer::ISRC_NUMBER);
			$criteria->addSelectColumn(CcFilesPeer::CATALOG_NUMBER);
			$criteria->addSelectColumn(CcFilesPeer::ORIGINAL_ARTIST);
			$criteria->addSelectColumn(CcFilesPeer::COPYRIGHT);
			$criteria->addSelectColumn(CcFilesPeer::REPORT_DATETIME);
			$criteria->addSelectColumn(CcFilesPeer::REPORT_LOCATION);
			$criteria->addSelectColumn(CcFilesPeer::REPORT_ORGANIZATION);
			$criteria->addSelectColumn(CcFilesPeer::SUBJECT);
			$criteria->addSelectColumn(CcFilesPeer::CONTRIBUTOR);
			$criteria->addSelectColumn(CcFilesPeer::LANGUAGE);
			$criteria->addSelectColumn(CcFilesPeer::FILE_EXISTS);
			$criteria->addSelectColumn(CcFilesPeer::SOUNDCLOUD_ID);
			$criteria->addSelectColumn(CcFilesPeer::SOUNDCLOUD_ERROR_CODE);
			$criteria->addSelectColumn(CcFilesPeer::SOUNDCLOUD_ERROR_MSG);
			$criteria->addSelectColumn(CcFilesPeer::SOUNDCLOUD_LINK_TO_FILE);
			$criteria->addSelectColumn(CcFilesPeer::SOUNDCLOUD_UPLOAD_TIME);
			$criteria->addSelectColumn(CcFilesPeer::REPLAY_GAIN);
			$criteria->addSelectColumn(CcFilesPeer::OWNER_ID);
			$criteria->addSelectColumn(CcFilesPeer::CUEIN);
			$criteria->addSelectColumn(CcFilesPeer::CUEOUT);
			$criteria->addSelectColumn(CcFilesPeer::SILAN_CHECK);
			$criteria->addSelectColumn(CcFilesPeer::HIDDEN);
			$criteria->addSelectColumn(CcFilesPeer::IS_SCHEDULED);
			$criteria->addSelectColumn(CcFilesPeer::IS_PLAYLIST);
		} else {
			$criteria->addSelectColumn($alias . '.ID');
			$criteria->addSelectColumn($alias . '.NAME');
			$criteria->addSelectColumn($alias . '.MIME');
			$criteria->addSelectColumn($alias . '.FTYPE');
			$criteria->addSelectColumn($alias . '.DIRECTORY');
			$criteria->addSelectColumn($alias . '.FILEPATH');
			$criteria->addSelectColumn($alias . '.STATE');
			$criteria->addSelectColumn($alias . '.CURRENTLYACCESSING');
			$criteria->addSelectColumn($alias . '.EDITEDBY');
			$criteria->addSelectColumn($alias . '.MTIME');
			$criteria->addSelectColumn($alias . '.UTIME');
			$criteria->addSelectColumn($alias . '.LPTIME');
			$criteria->addSelectColumn($alias . '.MD5');
			$criteria->addSelectColumn($alias . '.TRACK_TITLE');
			$criteria->addSelectColumn($alias . '.ARTIST_NAME');
			$criteria->addSelectColumn($alias . '.BIT_RATE');
			$criteria->addSelectColumn($alias . '.SAMPLE_RATE');
			$criteria->addSelectColumn($alias . '.FORMAT');
			$criteria->addSelectColumn($alias . '.LENGTH');
			$criteria->addSelectColumn($alias . '.ALBUM_TITLE');
			$criteria->addSelectColumn($alias . '.GENRE');
			$criteria->addSelectColumn($alias . '.COMMENTS');
			$criteria->addSelectColumn($alias . '.YEAR');
			$criteria->addSelectColumn($alias . '.TRACK_NUMBER');
			$criteria->addSelectColumn($alias . '.CHANNELS');
			$criteria->addSelectColumn($alias . '.URL');
			$criteria->addSelectColumn($alias . '.BPM');
			$criteria->addSelectColumn($alias . '.RATING');
			$criteria->addSelectColumn($alias . '.ENCODED_BY');
			$criteria->addSelectColumn($alias . '.DISC_NUMBER');
			$criteria->addSelectColumn($alias . '.MOOD');
			$criteria->addSelectColumn($alias . '.LABEL');
			$criteria->addSelectColumn($alias . '.COMPOSER');
			$criteria->addSelectColumn($alias . '.ENCODER');
			$criteria->addSelectColumn($alias . '.CHECKSUM');
			$criteria->addSelectColumn($alias . '.LYRICS');
			$criteria->addSelectColumn($alias . '.ORCHESTRA');
			$criteria->addSelectColumn($alias . '.CONDUCTOR');
			$criteria->addSelectColumn($alias . '.LYRICIST');
			$criteria->addSelectColumn($alias . '.ORIGINAL_LYRICIST');
			$criteria->addSelectColumn($alias . '.RADIO_STATION_NAME');
			$criteria->addSelectColumn($alias . '.INFO_URL');
			$criteria->addSelectColumn($alias . '.ARTIST_URL');
			$criteria->addSelectColumn($alias . '.AUDIO_SOURCE_URL');
			$criteria->addSelectColumn($alias . '.RADIO_STATION_URL');
			$criteria->addSelectColumn($alias . '.BUY_THIS_URL');
			$criteria->addSelectColumn($alias . '.ISRC_NUMBER');
			$criteria->addSelectColumn($alias . '.CATALOG_NUMBER');
			$criteria->addSelectColumn($alias . '.ORIGINAL_ARTIST');
			$criteria->addSelectColumn($alias . '.COPYRIGHT');
			$criteria->addSelectColumn($alias . '.REPORT_DATETIME');
			$criteria->addSelectColumn($alias . '.REPORT_LOCATION');
			$criteria->addSelectColumn($alias . '.REPORT_ORGANIZATION');
			$criteria->addSelectColumn($alias . '.SUBJECT');
			$criteria->addSelectColumn($alias . '.CONTRIBUTOR');
			$criteria->addSelectColumn($alias . '.LANGUAGE');
			$criteria->addSelectColumn($alias . '.FILE_EXISTS');
			$criteria->addSelectColumn($alias . '.SOUNDCLOUD_ID');
			$criteria->addSelectColumn($alias . '.SOUNDCLOUD_ERROR_CODE');
			$criteria->addSelectColumn($alias . '.SOUNDCLOUD_ERROR_MSG');
			$criteria->addSelectColumn($alias . '.SOUNDCLOUD_LINK_TO_FILE');
			$criteria->addSelectColumn($alias . '.SOUNDCLOUD_UPLOAD_TIME');
			$criteria->addSelectColumn($alias . '.REPLAY_GAIN');
			$criteria->addSelectColumn($alias . '.OWNER_ID');
			$criteria->addSelectColumn($alias . '.CUEIN');
			$criteria->addSelectColumn($alias . '.CUEOUT');
			$criteria->addSelectColumn($alias . '.SILAN_CHECK');
			$criteria->addSelectColumn($alias . '.HIDDEN');
			$criteria->addSelectColumn($alias . '.IS_SCHEDULED');
			$criteria->addSelectColumn($alias . '.IS_PLAYLIST');
		}
	}

	/**
	 * Returns the number of rows matching criteria.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @return     int Number of matching rows.
	 */
	public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
	{
		// we may modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CcFilesPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcFilesPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName

		if ($con === null) {
			$con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
		// BasePeer returns a PDOStatement
		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}
	/**
	 * Method to select one object from the DB.
	 *
	 * @param      Criteria $criteria object used to create the SELECT statement.
	 * @param      PropelPDO $con
	 * @return     CcFiles
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = CcFilesPeer::doSelect($critcopy, $con);
		if ($objects) {
			return $objects[0];
		}
		return null;
	}
	/**
	 * Method to do selects.
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      PropelPDO $con
	 * @return     array Array of selected Objects
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelect(Criteria $criteria, PropelPDO $con = null)
	{
		return CcFilesPeer::populateObjects(CcFilesPeer::doSelectStmt($criteria, $con));
	}
	/**
	 * Prepares the Criteria object and uses the parent doSelect() method to execute a PDOStatement.
	 *
	 * Use this method directly if you want to work with an executed statement durirectly (for example
	 * to perform your own object hydration).
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      PropelPDO $con The connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 * @return     PDOStatement The executed PDOStatement object.
	 * @see        BasePeer::doSelect()
	 */
	public static function doSelectStmt(Criteria $criteria, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		if (!$criteria->hasSelectClause()) {
			$criteria = clone $criteria;
			CcFilesPeer::addSelectColumns($criteria);
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// BasePeer returns a PDOStatement
		return BasePeer::doSelect($criteria, $con);
	}
	/**
	 * Adds an object to the instance pool.
	 *
	 * Propel keeps cached copies of objects in an instance pool when they are retrieved
	 * from the database.  In some cases -- especially when you override doSelect*()
	 * methods in your stub classes -- you may need to explicitly add objects
	 * to the cache in order to ensure that the same objects are always returned by doSelect*()
	 * and retrieveByPK*() calls.
	 *
	 * @param      CcFiles $value A CcFiles object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(CcFiles $obj, $key = null)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if ($key === null) {
				$key = (string) $obj->getDbId();
			} // if key === null
			self::$instances[$key] = $obj;
		}
	}

	/**
	 * Removes an object from the instance pool.
	 *
	 * Propel keeps cached copies of objects in an instance pool when they are retrieved
	 * from the database.  In some cases -- especially when you override doDelete
	 * methods in your stub classes -- you may need to explicitly remove objects
	 * from the cache in order to prevent returning objects that no longer exist.
	 *
	 * @param      mixed $value A CcFiles object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof CcFiles) {
				$key = (string) $value->getDbId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or CcFiles object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
				throw $e;
			}

			unset(self::$instances[$key]);
		}
	} // removeInstanceFromPool()

	/**
	 * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
	 *
	 * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
	 * a multi-column primary key, a serialize()d version of the primary key will be returned.
	 *
	 * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
	 * @return     CcFiles Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
	 * @see        getPrimaryKeyHash()
	 */
	public static function getInstanceFromPool($key)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if (isset(self::$instances[$key])) {
				return self::$instances[$key];
			}
		}
		return null; // just to be explicit
	}
	
	/**
	 * Clear the instance pool.
	 *
	 * @return     void
	 */
	public static function clearInstancePool()
	{
		self::$instances = array();
	}
	
	/**
	 * Method to invalidate the instance pool of all tables related to cc_files
	 * by a foreign key with ON DELETE CASCADE
	 */
	public static function clearRelatedInstancePool()
	{
		// Invalidate objects in CcShowInstancesPeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		CcShowInstancesPeer::clearInstancePool();
		// Invalidate objects in CcPlaylistcontentsPeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		CcPlaylistcontentsPeer::clearInstancePool();
		// Invalidate objects in CcBlockcontentsPeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		CcBlockcontentsPeer::clearInstancePool();
		// Invalidate objects in CcSchedulePeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		CcSchedulePeer::clearInstancePool();
		// Invalidate objects in CcPlayoutHistoryPeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		CcPlayoutHistoryPeer::clearInstancePool();
	}

	/**
	 * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
	 *
	 * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
	 * a multi-column primary key, a serialize()d version of the primary key will be returned.
	 *
	 * @param      array $row PropelPDO resultset row.
	 * @param      int $startcol The 0-based offset for reading from the resultset row.
	 * @return     string A string version of PK or NULL if the components of primary key in result array are all null.
	 */
	public static function getPrimaryKeyHashFromRow($row, $startcol = 0)
	{
		// If the PK cannot be derived from the row, return NULL.
		if ($row[$startcol] === null) {
			return null;
		}
		return (string) $row[$startcol];
	}

	/**
	 * Retrieves the primary key from the DB resultset row 
	 * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
	 * a multi-column primary key, an array of the primary key columns will be returned.
	 *
	 * @param      array $row PropelPDO resultset row.
	 * @param      int $startcol The 0-based offset for reading from the resultset row.
	 * @return     mixed The primary key of the row
	 */
	public static function getPrimaryKeyFromRow($row, $startcol = 0)
	{
		return (int) $row[$startcol];
	}
	
	/**
	 * The returned array will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function populateObjects(PDOStatement $stmt)
	{
		$results = array();
	
		// set the class once to avoid overhead in the loop
		$cls = CcFilesPeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = CcFilesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = CcFilesPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				CcFilesPeer::addInstanceToPool($obj, $key);
			} // if key exists
		}
		$stmt->closeCursor();
		return $results;
	}
	/**
	 * Populates an object of the default type or an object that inherit from the default.
	 *
	 * @param      array $row PropelPDO resultset row.
	 * @param      int $startcol The 0-based offset for reading from the resultset row.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 * @return     array (CcFiles object, last column rank)
	 */
	public static function populateObject($row, $startcol = 0)
	{
		$key = CcFilesPeer::getPrimaryKeyHashFromRow($row, $startcol);
		if (null !== ($obj = CcFilesPeer::getInstanceFromPool($key))) {
			// We no longer rehydrate the object, since this can cause data loss.
			// See http://www.propelorm.org/ticket/509
			// $obj->hydrate($row, $startcol, true); // rehydrate
			$col = $startcol + CcFilesPeer::NUM_COLUMNS;
		} else {
			$cls = CcFilesPeer::OM_CLASS;
			$obj = new $cls();
			$col = $obj->hydrate($row, $startcol);
			CcFilesPeer::addInstanceToPool($obj, $key);
		}
		return array($obj, $col);
	}

	/**
	 * Returns the number of rows matching criteria, joining the related FkOwner table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinFkOwner(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CcFilesPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcFilesPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(CcFilesPeer::OWNER_ID, CcSubjsPeer::ID, $join_behavior);

		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related CcSubjsRelatedByDbEditedby table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinCcSubjsRelatedByDbEditedby(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CcFilesPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcFilesPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(CcFilesPeer::EDITEDBY, CcSubjsPeer::ID, $join_behavior);

		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related CcMusicDirs table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinCcMusicDirs(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CcFilesPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcFilesPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(CcFilesPeer::DIRECTORY, CcMusicDirsPeer::ID, $join_behavior);

		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Selects a collection of CcFiles objects pre-filled with their CcSubjs objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcFiles objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinFkOwner(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CcFilesPeer::addSelectColumns($criteria);
		$startcol = (CcFilesPeer::NUM_COLUMNS - CcFilesPeer::NUM_LAZY_LOAD_COLUMNS);
		CcSubjsPeer::addSelectColumns($criteria);

		$criteria->addJoin(CcFilesPeer::OWNER_ID, CcSubjsPeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcFilesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcFilesPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = CcFilesPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcFilesPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = CcSubjsPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = CcSubjsPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = CcSubjsPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					CcSubjsPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (CcFiles) to $obj2 (CcSubjs)
				$obj2->addCcFilesRelatedByDbOwnerId($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CcFiles objects pre-filled with their CcSubjs objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcFiles objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinCcSubjsRelatedByDbEditedby(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CcFilesPeer::addSelectColumns($criteria);
		$startcol = (CcFilesPeer::NUM_COLUMNS - CcFilesPeer::NUM_LAZY_LOAD_COLUMNS);
		CcSubjsPeer::addSelectColumns($criteria);

		$criteria->addJoin(CcFilesPeer::EDITEDBY, CcSubjsPeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcFilesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcFilesPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = CcFilesPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcFilesPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = CcSubjsPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = CcSubjsPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = CcSubjsPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					CcSubjsPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (CcFiles) to $obj2 (CcSubjs)
				$obj2->addCcFilesRelatedByDbEditedby($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CcFiles objects pre-filled with their CcMusicDirs objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcFiles objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinCcMusicDirs(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CcFilesPeer::addSelectColumns($criteria);
		$startcol = (CcFilesPeer::NUM_COLUMNS - CcFilesPeer::NUM_LAZY_LOAD_COLUMNS);
		CcMusicDirsPeer::addSelectColumns($criteria);

		$criteria->addJoin(CcFilesPeer::DIRECTORY, CcMusicDirsPeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcFilesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcFilesPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = CcFilesPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcFilesPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = CcMusicDirsPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = CcMusicDirsPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = CcMusicDirsPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					CcMusicDirsPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (CcFiles) to $obj2 (CcMusicDirs)
				$obj2->addCcFiles($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining all related tables
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAll(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CcFilesPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcFilesPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(CcFilesPeer::OWNER_ID, CcSubjsPeer::ID, $join_behavior);

		$criteria->addJoin(CcFilesPeer::EDITEDBY, CcSubjsPeer::ID, $join_behavior);

		$criteria->addJoin(CcFilesPeer::DIRECTORY, CcMusicDirsPeer::ID, $join_behavior);

		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}

	/**
	 * Selects a collection of CcFiles objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcFiles objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CcFilesPeer::addSelectColumns($criteria);
		$startcol2 = (CcFilesPeer::NUM_COLUMNS - CcFilesPeer::NUM_LAZY_LOAD_COLUMNS);

		CcSubjsPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (CcSubjsPeer::NUM_COLUMNS - CcSubjsPeer::NUM_LAZY_LOAD_COLUMNS);

		CcSubjsPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (CcSubjsPeer::NUM_COLUMNS - CcSubjsPeer::NUM_LAZY_LOAD_COLUMNS);

		CcMusicDirsPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (CcMusicDirsPeer::NUM_COLUMNS - CcMusicDirsPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(CcFilesPeer::OWNER_ID, CcSubjsPeer::ID, $join_behavior);

		$criteria->addJoin(CcFilesPeer::EDITEDBY, CcSubjsPeer::ID, $join_behavior);

		$criteria->addJoin(CcFilesPeer::DIRECTORY, CcMusicDirsPeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcFilesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcFilesPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = CcFilesPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcFilesPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

			// Add objects for joined CcSubjs rows

			$key2 = CcSubjsPeer::getPrimaryKeyHashFromRow($row, $startcol2);
			if ($key2 !== null) {
				$obj2 = CcSubjsPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = CcSubjsPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					CcSubjsPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 loaded

				// Add the $obj1 (CcFiles) to the collection in $obj2 (CcSubjs)
				$obj2->addCcFilesRelatedByDbOwnerId($obj1);
			} // if joined row not null

			// Add objects for joined CcSubjs rows

			$key3 = CcSubjsPeer::getPrimaryKeyHashFromRow($row, $startcol3);
			if ($key3 !== null) {
				$obj3 = CcSubjsPeer::getInstanceFromPool($key3);
				if (!$obj3) {

					$cls = CcSubjsPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					CcSubjsPeer::addInstanceToPool($obj3, $key3);
				} // if obj3 loaded

				// Add the $obj1 (CcFiles) to the collection in $obj3 (CcSubjs)
				$obj3->addCcFilesRelatedByDbEditedby($obj1);
			} // if joined row not null

			// Add objects for joined CcMusicDirs rows

			$key4 = CcMusicDirsPeer::getPrimaryKeyHashFromRow($row, $startcol4);
			if ($key4 !== null) {
				$obj4 = CcMusicDirsPeer::getInstanceFromPool($key4);
				if (!$obj4) {

					$cls = CcMusicDirsPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					CcMusicDirsPeer::addInstanceToPool($obj4, $key4);
				} // if obj4 loaded

				// Add the $obj1 (CcFiles) to the collection in $obj4 (CcMusicDirs)
				$obj4->addCcFiles($obj1);
			} // if joined row not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related FkOwner table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptFkOwner(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CcFilesPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcFilesPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(CcFilesPeer::DIRECTORY, CcMusicDirsPeer::ID, $join_behavior);

		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related CcSubjsRelatedByDbEditedby table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptCcSubjsRelatedByDbEditedby(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CcFilesPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcFilesPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(CcFilesPeer::DIRECTORY, CcMusicDirsPeer::ID, $join_behavior);

		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related CcMusicDirs table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptCcMusicDirs(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CcFilesPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcFilesPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(CcFilesPeer::OWNER_ID, CcSubjsPeer::ID, $join_behavior);

		$criteria->addJoin(CcFilesPeer::EDITEDBY, CcSubjsPeer::ID, $join_behavior);

		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Selects a collection of CcFiles objects pre-filled with all related objects except FkOwner.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcFiles objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptFkOwner(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CcFilesPeer::addSelectColumns($criteria);
		$startcol2 = (CcFilesPeer::NUM_COLUMNS - CcFilesPeer::NUM_LAZY_LOAD_COLUMNS);

		CcMusicDirsPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (CcMusicDirsPeer::NUM_COLUMNS - CcMusicDirsPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(CcFilesPeer::DIRECTORY, CcMusicDirsPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcFilesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcFilesPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = CcFilesPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcFilesPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined CcMusicDirs rows

				$key2 = CcMusicDirsPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = CcMusicDirsPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = CcMusicDirsPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					CcMusicDirsPeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (CcFiles) to the collection in $obj2 (CcMusicDirs)
				$obj2->addCcFiles($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CcFiles objects pre-filled with all related objects except CcSubjsRelatedByDbEditedby.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcFiles objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptCcSubjsRelatedByDbEditedby(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CcFilesPeer::addSelectColumns($criteria);
		$startcol2 = (CcFilesPeer::NUM_COLUMNS - CcFilesPeer::NUM_LAZY_LOAD_COLUMNS);

		CcMusicDirsPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (CcMusicDirsPeer::NUM_COLUMNS - CcMusicDirsPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(CcFilesPeer::DIRECTORY, CcMusicDirsPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcFilesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcFilesPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = CcFilesPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcFilesPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined CcMusicDirs rows

				$key2 = CcMusicDirsPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = CcMusicDirsPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = CcMusicDirsPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					CcMusicDirsPeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (CcFiles) to the collection in $obj2 (CcMusicDirs)
				$obj2->addCcFiles($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CcFiles objects pre-filled with all related objects except CcMusicDirs.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcFiles objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptCcMusicDirs(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CcFilesPeer::addSelectColumns($criteria);
		$startcol2 = (CcFilesPeer::NUM_COLUMNS - CcFilesPeer::NUM_LAZY_LOAD_COLUMNS);

		CcSubjsPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (CcSubjsPeer::NUM_COLUMNS - CcSubjsPeer::NUM_LAZY_LOAD_COLUMNS);

		CcSubjsPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (CcSubjsPeer::NUM_COLUMNS - CcSubjsPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(CcFilesPeer::OWNER_ID, CcSubjsPeer::ID, $join_behavior);

		$criteria->addJoin(CcFilesPeer::EDITEDBY, CcSubjsPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcFilesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcFilesPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = CcFilesPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcFilesPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined CcSubjs rows

				$key2 = CcSubjsPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = CcSubjsPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = CcSubjsPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					CcSubjsPeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (CcFiles) to the collection in $obj2 (CcSubjs)
				$obj2->addCcFilesRelatedByDbOwnerId($obj1);

			} // if joined row is not null

				// Add objects for joined CcSubjs rows

				$key3 = CcSubjsPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = CcSubjsPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = CcSubjsPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					CcSubjsPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (CcFiles) to the collection in $obj3 (CcSubjs)
				$obj3->addCcFilesRelatedByDbEditedby($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}

	/**
	 * Returns the TableMap related to this peer.
	 * This method is not needed for general use but a specific application could have a need.
	 * @return     TableMap
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getTableMap()
	{
		return Propel::getDatabaseMap(self::DATABASE_NAME)->getTable(self::TABLE_NAME);
	}

	/**
	 * Add a TableMap instance to the database for this peer class.
	 */
	public static function buildTableMap()
	{
	  $dbMap = Propel::getDatabaseMap(BaseCcFilesPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseCcFilesPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new CcFilesTableMap());
	  }
	}

	/**
	 * The class that the Peer will make instances of.
	 *
	 * If $withPrefix is true, the returned path
	 * uses a dot-path notation which is tranalted into a path
	 * relative to a location on the PHP include_path.
	 * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
	 *
	 * @param      boolean $withPrefix Whether or not to return the path with the class name
	 * @return     string path.to.ClassName
	 */
	public static function getOMClass($withPrefix = true)
	{
		return $withPrefix ? CcFilesPeer::CLASS_DEFAULT : CcFilesPeer::OM_CLASS;
	}

	/**
	 * Method perform an INSERT on the database, given a CcFiles or Criteria object.
	 *
	 * @param      mixed $values Criteria or CcFiles object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from CcFiles object
		}

		if ($criteria->containsKey(CcFilesPeer::ID) && $criteria->keyContainsValue(CcFilesPeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.CcFilesPeer::ID.')');
		}


		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		try {
			// use transaction because $criteria could contain info
			// for more than one table (I guess, conceivably)
			$con->beginTransaction();
			$pk = BasePeer::doInsert($criteria, $con);
			$con->commit();
		} catch(PropelException $e) {
			$con->rollBack();
			throw $e;
		}

		return $pk;
	}

	/**
	 * Method perform an UPDATE on the database, given a CcFiles or Criteria object.
	 *
	 * @param      mixed $values Criteria or CcFiles object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(CcFilesPeer::ID);
			$value = $criteria->remove(CcFilesPeer::ID);
			if ($value) {
				$selectCriteria->add(CcFilesPeer::ID, $value, $comparison);
			} else {
				$selectCriteria->setPrimaryTableName(CcFilesPeer::TABLE_NAME);
			}

		} else { // $values is CcFiles object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the cc_files table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(CcFilesPeer::TABLE_NAME, $con, CcFilesPeer::DATABASE_NAME);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			CcFilesPeer::clearInstancePool();
			CcFilesPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a CcFiles or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or CcFiles object or primary key or array of primary keys
	 *              which is used to create the DELETE statement
	 * @param      PropelPDO $con the connection to use
	 * @return     int 	The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
	 *				if supported by native driver or if emulated using Propel.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	 public static function doDelete($values, PropelPDO $con = null)
	 {
		if ($con === null) {
			$con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			CcFilesPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof CcFiles) { // it's a model object
			// invalidate the cache for this single object
			CcFilesPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(CcFilesPeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				CcFilesPeer::removeInstanceFromPool($singleval);
			}
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		$affectedRows = 0; // initialize var to track total num of affected rows

		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			
			$affectedRows += BasePeer::doDelete($criteria, $con);
			CcFilesPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given CcFiles object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      CcFiles $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(CcFiles $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(CcFilesPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(CcFilesPeer::TABLE_NAME);

			if (! is_array($cols)) {
				$cols = array($cols);
			}

			foreach ($cols as $colName) {
				if ($tableMap->containsColumn($colName)) {
					$get = 'get' . $tableMap->getColumn($colName)->getPhpName();
					$columns[$colName] = $obj->$get();
				}
			}
		} else {

		}

		return BasePeer::doValidate(CcFilesPeer::DATABASE_NAME, CcFilesPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     CcFiles
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = CcFilesPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		if ($con === null) {
			$con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria = new Criteria(CcFilesPeer::DATABASE_NAME);
		$criteria->add(CcFilesPeer::ID, $pk);

		$v = CcFilesPeer::doSelect($criteria, $con);

		return !empty($v) > 0 ? $v[0] : null;
	}

	/**
	 * Retrieve multiple objects by pkey.
	 *
	 * @param      array $pks List of primary keys
	 * @param      PropelPDO $con the connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function retrieveByPKs($pks, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$objs = null;
		if (empty($pks)) {
			$objs = array();
		} else {
			$criteria = new Criteria(CcFilesPeer::DATABASE_NAME);
			$criteria->add(CcFilesPeer::ID, $pks, Criteria::IN);
			$objs = CcFilesPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseCcFilesPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseCcFilesPeer::buildTableMap();

