<!-- Started at 11:39 6-20-2022 -->

<?php include("includes/header.php"); ?>

<h1 class="pageHeadingBig">You Might Also Like</h1>

<div class="gridViewContainer">

    <?php
        $albumQuery = mysqli_query($con, "SELECT * FROM albums ORDER BY RAND() LIMIT 10");

        while($row = mysqli_fetch_array($albumQuery)) {
            



            echo "<div class='gridViewItem'>
                    <a href='album.php?id=" . $row['id'] . "'>
                        <img src='" . $row['artworkPath'] . "'>

                        <div class='gridViewInfo'>"
                            . $row['title'] .
                        "</div>
                    </a>

                </div>";



        }
    ?>

</div>





<?php include("includes/footer.php"); ?>

<?php include("includes/header.php"); 

if(isset($_GET['id'])) {
	$albumId = $_GET['id'];
}
else {
	header("Location: index.php");
}

$album = new Album($con, $albumId);
$artist = $album->getArtist();
?>

<div class="entityInfo">

	<div class="leftSection">
		<img src="<?php echo $album->getArtworkPath(); ?>">
	</div>

	<div class="rightSection">
		<h2><?php echo $album->getTitle(); ?></h2>
		<p>By <?php echo $artist->getName(); ?></p>
		<p><?php echo $album->getNumberOfSongs(); ?> songs</p>

	</div>

</div>


<div class="tracklistContainer">
	<ul class="tracklist">
		
		<?php
		$songIdArray = $album->getSongIds();

		$i = 1;
		foreach($songIdArray as $songId) {

			$albumSong = new Song($con, $songId);
			$albumArtist = $albumSong->getArtist();

			echo "<li class='tracklistRow'>
					<div class='trackCount'>
						<img class='play' src='assets/images/icons/play-white.png'>
						<span class='trackNumber'>$i</span>
					</div>


					<div class='trackInfo'>
						<span class='trackName'>" . $albumSong->getTitle() . "</span>
						<span class='artistName'>" . $albumArtist->getName() . "</span>
					</div>

					<div class='trackOptions'> 
						<img class='optionsButton' src='assets/images/icons/more.png'>
					 </div>

					 <div class='trackDuration'>
					 	<span class='duration'>" . $albumSong->getDuration() . "</span>
					 </div>

				</li>";

			$i = $i + 1;



		}

		?>



	</ul>
</div>


<?php include("includes/footer.php"); ?>

var currentPlaylist = [];
var audioElement;


function Audio() {

	this.currentlyPlaying;
	this.audio = document.createElement('audio');

	this.setTrack = function(src) {
		this.audio.src = src;
	}

	this.play = function() {
		this.audio.play();
	}

	this.pause = function () {
		this.audio.pause();
	}
	
}

<?php
include("includes/config.php");
include("includes/classes/Artist.php");
include("includes/classes/Album.php");
include("includes/classes/Song.php");

//session_destroy(); LOGOUT

if(isset($_SESSION['userLoggedIn'])) {
    $userLoggedIn = $_SESSION['userLoggedIn'];
}
else {
    header("Location: register.php");
}

?>

<html>
<head>
    <title>Welcome to Slotify!</title>

    <link rel="stylesheet" type="text/css" href="assets/css/style.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="assets/js/script.js"></script>
</head>

<body>

    <div id="mainContainer">

        <div id="topContainer">

            <?php include("includes/navBarContainer.php"); ?>

            <div id="mainViewContainer">

                <div id="mainContent">
                  
<?php
$songQuery = mysqli_query($con, "SELECT id FROM songs ORDER BY RAND() LIMIT 10");

$resultArray = array();

while($row = mysqli_fetch_array($songQuery)) {
    array_push($resultArray, $row['id']);
}

$jsonArray = json_encode($resultArray);
?>

<script>

$(document).ready(function() {
    currentPlaylist = <?php echo $jsonArray; ?>;
    audioElement = new Audio();
    setTrack(currentPlaylist[0], currentPlaylist, false);
});


function setTrack(trackId, newPlaylist, play) {

    $.post("includes/handlers/ajax/getSongJson.php", { songId: trackId }, function(data) {

        var track = JSON.parse(data);

        console.log(track);
        audioElement.setTrack(track.path);
        audioElement.play();
    })

    if(play == true) {
        audioElement.play();
    }
}

function playSong() {
    $(".controlButton.play").hide();
    $(".controlButton.pause").show();
    audioElement.play();
}

function pauseSong() {
    $(".controlButton.play").show();
    $(".controlButton.pause").hide();
    audioElement.pause();
}

</script>


<div id="nowPlayingBarContainer">

    <div id="nowPlayingBar">

        <div id="nowPlayingLeft">
            <div class="content">
                <span class="albumLink">
                    <img src="assets/images/artwork/Decade.jpg" class="albumArtwork">
                </span>

                <div class="trackInfo">

                    <span class="trackName">
                        <span>I Don't Want Your Love</span>
                    </span>

                    <span class="artistName">
                        <span>Duran Duran</span>
                    </span>

                </div>



            </div>
        </div>

        <div id="nowPlayingCenter">

            <div class="content playerControls">

                <div class="buttons">

                    <button class="controlButton shuffle" title="Shuffle button">
                        <img src="assets/images/icons/shuffle.png" alt="Shuffle">
                    </button>

                    <button class="controlButton previous" title="Previous button">
                        <img src="assets/images/icons/previous.png" alt="Previous">
                    </button>

                    <button class="controlButton play" title="Play button" onclick="playSong()">
                        <img src="assets/images/icons/play.png" alt="Play">
                    </button>

                    <button class="controlButton pause" title="Pause button" style="display: none;" onclick="pauseSong()">
                        <img src="assets/images/icons/pause.png" alt="Pause">
                    </button>

                    <button class="controlButton next" title="Next button">
                        <img src="assets/images/icons/next.png" alt="Next">
                    </button>

                    <button class="controlButton repeat" title="Repeat button">
                        <img src="assets/images/icons/repeat.png" alt="Repeat">
                    </button>

                </div>


                <div class="playbackBar">

                    <span class="progressTime current">0.00</span>

                    <div class="progressBar">
                        <div class="progressBarBg">
                            <div class="progress"></div>
                        </div>
                    </div>

                    <span class="progressTime remaining">0.00</span>


                </div>


            </div>


        </div>

        <div id="nowPlayingRight">
            <div class="volumeBar">

                <button class="controlButton volume" title="Volume button">
                    <img src="assets/images/icons/volume.png" alt="Volume">
                </button>

                <div class="progressBar">
                    <div class="progressBarBg">
                        <div class="progress"></div>
                    </div>
                </div>

            </div>
        </div>




    </div>

</div>
                  
<?php
include("../../config.php");

if(isset($_POST['songId'])) {
	$songId = $_POST['songId'];

	$query = mysqli_query($con, "SELECT * FROM songs WHERE id='$songId'");

	$resultArray = mysqli_fetch_array($query);

	echo json_encode($resultArray);
}


?>
                  
<?php
	class Album {

		private $con;
		private $id;
		private $title;
		private $artistId;
		private $genre;
		private $artworkPath;

		public function __construct($con, $id) {
			$this->con = $con;
			$this->id = $id;

			$query = mysqli_query($this->con, "SELECT * FROM albums WHERE id='$this->id'");
			$album = mysqli_fetch_array($query);

			$this->title = $album['title'];
			$this->artistId = $album['artist'];
			$this->genre = $album['genre'];
			$this->artworkPath = $album['artworkPath'];


		}

		public function getTitle() {
			return $this->title;
		}

		public function getArtist() {
			return new Artist($this->con, $this->artistId);
		}

		public function getGenre() {
			return $this->genre;
		}

		public function getArtworkPath() {
			return $this->artworkPath;
		}

		public function getNumberOfSongs() {
			$query = mysqli_query($this->con, "SELECT id FROM songs WHERE album='$this->id'");
			return mysqli_num_rows($query);
		}

		public function getSongIds() {

			$query = mysqli_query($this->con, "SELECT id FROM songs WHERE album='$this->id' ORDER BY albumOrder ASC");

			$array = array();

			while($row = mysqli_fetch_array($query)) {
				array_push($array, $row['id']);
			}

			return $array;

		}
	}
?>
                  
<?php
	class Song {

		private $con;
		private $id;
		private $mysqliData;
		private $title;
		private $artistId;
		private $albumId;
		private $genre;
		private $duration;
		private $path;

		public function __construct($con, $id) {
			$this->con = $con;
			$this->id = $id;

			$query = mysqli_query($this->con, "SELECT * FROM songs WHERE id='$this->id'");
			$this->mysqliData = mysqli_fetch_array($query);
			$this->title = $this->mysqliData['title'];
			$this->artistId = $this->mysqliData['artist'];
			$this->albumId = $this->mysqliData['album'];
			$this->genre = $this->mysqliData['genre'];
			$this->duration = $this->mysqliData['duration'];
			$this->path = $this->mysqliData['path'];
		}

		public function getTitle() {
			return $this->title;
		}

		public function getArtist() {
			return new Artist($this->con, $this->artistId);
		}

		public function getAlbum() {
			return new Album($this->con, $this->albumId);
		}

		public function getPath() {
			return $this->path;
		}

		public function getDuration() {
			return $this->duration;
		}

		public function getMysqliData() {
			return $this->mysqliData;
		}

		public function getGenre() {
			return $this->genre;
		}


	}
?>
                  
<?php
	class Artist {

		private $con;
		private $id;

		public function __construct($con, $id) {
			$this->con = $con;
			$this->id = $id;
		}

		public function getName() {
			$artistQuery = mysqli_query($this->con, "SELECT name FROM artists WHERE id='$this->id'");
			$artist = mysqli_fetch_array($artistQuery);
			return $artist['name'];
		}
	}
?>
                  
html, body {
    padding:  0;
    margin: 0;
    height: 100%;
}

* {
    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
    color: #fff;
    letter-spacing: 1px;
}

body {
    background-color: #181818;
    font-size: 14px;
    min-width: 720px;
}

#nowPlayingBarContainer {
    width: 100%;
    background-color: #282828;
    bottom: 0;
    position: fixed;
    min-width: 620px;
}

#nowPlayingBar {
    display: flex;
    height: 90px;
    padding: 16px;
    box-sizing: border-box;
}

#nowPlayingLeft,
#nowPlayingRight {
    width: 30%;
    min-width: 180px;
}

#nowPlayingRight {
    position: relative;
    margin-top: 16px;
}

#nowPlayingCenter {
    width: 40%;
    max-width: 700px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

#nowPlayingBar .content {
    width: 100%;
    height:  57px;
}

.playerControls .buttons {
    margin: 0 auto;
    display: table;
}

.controlButton {
    background-color: transparent;
    border: none;
    vertical-align: middle;
}

.controlButton img {
    width: 20px;
    height: 20px;
}

.controlButton.play img,
.controlButton.pause img {
    width: 32px;
    height: 32px;
}

.controlButton:hover {
    cursor: pointer;
}

.progressTime {
    color: #a0a0a0;
    font-size: 11px;
    min-width: 40px;
    text-align: center;
}

.playbackBar {
    display: flex;
}

.progressbar {
    width: 100%;
    height: 12px;
    display: inline-flex;
    cursor: pointer;
}

.progressBarBG {
    background-color: #404040;
    height: 4px;
    width: 100%;
    border-radius: 2px;
}

.progress {
    background-color: #a0a0a0;
    height: 4px;
    width: 0;
    border-radius: 2px;
}

.playbackBar .progressBar {
    margin-top: 3px;
}

#nowPlayingLeft .albumArtWork{
    height: 100%;
    max-width: 57px;
    margin-right: 15px;
    float: left;
    background-size: cover;
}

.trackInfo {
    display: table;
}

#nowPlayingLeft .trackInfo .trackName {
    margin: 6px 0;
    display: inline-block;
    width: 100%;
}

#nowPlayingLeft .trackInfo .artistName span {
    font-size: 12px;
    color: #a0a0a0;
}

.volumeBar {
    width: 180px;
    position: absolute;
    right: 0;
}

.volumeBar .progressBar {
    width: 125px;
}

#topContainer {
    min-height: 100%;
    width: 100%;
}

#navBarContainer {
    background-color: #000;
    width: 220px;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
}

.navBar {
    padding: 25px;
    display: flex;
    flex-direction: column;
    -ms-flex-direction: column;
}

.logo {
    margin-bottom: 15px;
}

.logo img {
    width: 32px;
}

.navBar .group {
    border-top: 1px solid #a0a0a0;
    padding: 10px 0;
}

.navItem {
    padding: 10px 0;
    font-size: 14px;
    font-weight: 500;
    display: block;
    letter-spacing: 1px;
    position: relative;
}

.navItemLink {
    color: #a0a0a0;
    text-decoration: none;
}

.navItemLink:hover {
    color: #fff;
}

.navItemLink .icon {
    position: absolute;
    right: 0;
    top: 6px;
    width: 25px;
}

#mainViewContainer {
    margin-left: 220px;
    padding-bottom: 90px;
    width: calc(100% - 220px);
}

#mainContent {
    padding: 0 20px;
}

.pageHeadingBig {
    padding: 20px;
    text-align: center;
}

.gridViewItem {
    display: inline-block;
    margin-right: 20px;
    width: 29%;
    max-width: 200px;
    min-width: 150px;
    margin-bottom: 20px;
}

.gridViewitem img {
    width: 100%;
}

.gridViewInfo {
    font-weight: 300;
    text-align: center;
    padding: 5px 0;
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
}

.gridViewItem a {
    text-decoration: none;
}

.entityInfo {
    padding: 40px 0 10px 0;
    display: inline-block;
    width: 100%;
}

.entityInfo .leftSection {
    width: 30%;
    float: left;
    max-width: 250px;
}

.entityInfo .leftSection img {
    width: 100%;
}

.entityInfo .rightSection {
    width: 70%;
    float: left;
    padding: 5px 10px 5px 40px;
    box-sizing: border-box;
}

.entityInfo .rightSection h2 {
    margin-top: 0px;
}

.entityInfo .rightSection p {
    color: #939393;
    font-weight: 200;
}

.tracklist {
  padding: 0;

}

.tracklistRow {    
    height: 40px;
    padding: 15px 10px;
    list-style: none;
}

.tracklistRow span {
    color: #939393;
    font-weight: 200;
}

.tracklistRow:hover {
    background-color: #282828;
}

.tracklistRow .trackCount {
    width: 8%;
    float: left;
}

.tracklistRow .trackCount img {
    width: 20px;
    visibility: hidden;
    position: absolute;
    cursor: pointer;
}

.tracklistRow:hover .trackCount img {
    visibility: visible;
}

.tracklistRow .trackCount span {
    visibility: visible;
}

.tracklistRow:hover .trackCount span {
    visibility: hidden;
}

.tracklistRow .trackInfo {
    width: 75%;
    float: left;
}

.tracklistRow .trackInfo span {
    display: block;
}

.tracklistRow .trackOptions {
    width: 5%;
    float: left;
    text-align: right;
}

.tracklistRow .trackOptions img {
    width: 15px;
    visibility: hidden;
}

.tracklistRow:hover .trackOptions img {
    visibility: visible;
}

.tracklistRow .trackDuration {
    width: 12%;
    float: left;
    text-align: right;
}

.tracklistRow .trackInfo .trackName {
    color: #fff;
    margin-bottom: 7px;
}
                  
<!-- Ended at 6:41 6-20-2022 -->
