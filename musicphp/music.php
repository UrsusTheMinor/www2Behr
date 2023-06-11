<?php

function connect() {
    $servername = "localhost";
    $username = "y23_2C_Behr";
    $password = "!password!";
    $dbname = "y23_2C_Behr";

    //create connection

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection error: " . $conn->connect_error);
    }

    return $conn;
}


function upload($conn) {
    $artist = $_POST["artist"];
    $album = $_POST["album"];
    $published = $_POST["datum"];
    $format = $_POST["typ"];

    $checkQuery = "SELECT * FROM music_collection WHERE artist = ? AND album = ? AND published = ? AND format = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ssss", $artist, $album, $published, $format);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows == 0) {
        $artistclear = htmlentities($artist);
        $albumclear = htmlentities($album);
        $insertQuery = "INSERT INTO music_collection (artist, album, published, format) VALUES (?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("ssss", $artistclear, $albumclear, $published, $format);
        $insertStmt->execute();
    }

    $checkStmt->close();
    $insertStmt->close();
}

function create_elements($conn) {
    $sql = "SELECT id, artist, album, published, format FROM music_collection";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        while($row = $result->fetch_assoc()) {
            if ($row["format"] == "c") {
                $format = "CD";
            } elseif ($row["format"] == "k") {
                $format = "Kasette";
            } elseif ($row["format"] == "v") {
                $format = "Vinyl (Schallplatte)";
            } elseif ($row["format"] == "d") {
                $format = "Digital";
            }  
            echo '
            <form class="post__element" action="music.php" method="post">
                        <p>' . $row["artist"] . '</p>
                        <p>' . $row["album"] . '</p>
                        <p>' . $row["published"] . '</p>
                        <p>' . $format . '</p>
                        <button type="submit" name="delete" value="' . $row["id"] . '" type="button" class="button" id=" ' . $row["id"] . '">
                            <span class="button__text">Delete</span>
                                <span class="button__icon">
                                    <ion-icon name="trash-outline"></ion-icon>
                            </span>
                        </button>
                    </form>
            ';
        }
    } else {
        echo "<p>0 results found</p>";
    }
}

function delete($conn, $id) {
    $sql = "DELETE FROM music_collection WHERE id = ?";
    $deleteStmt = $conn->prepare($sql);
    $deleteStmt->bind_param("i", $id);
    $deleteStmt->execute();
    $deleteStmt->close();
}


$conn = connect();
if (isset($_POST['delete'])) {
    $id = $_POST['delete'];
    delete($conn, $id);
}
if (!empty($_POST)) {
    upload($conn);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <style>

    a {
        color: #006e58;
        text-decoration: underline;
        cursor: pointer;
    }

    hr {
        margin: 20px;
        border: 0;
        border-top: 1px solid #ECECEC;
        box-sizing: content-box;
        overflow: visible;
    }

    .input-wrapper {
        position: relative;
        width: 80%;
    }

    input,select {
        color: rgba(17, 21, 28, 0.75);
        border: 1px solid rgb(118, 118, 118);
        border-radius: 6px;
        position: relative;
        width: 100%;
        margin: 10px;
        line-height: 6ex;
        box-sizing: border-box;
    }

    label {
        color: rgba(17, 21, 28, 0.75);
        position: absolute;
        top: 0.2ex;
        z-index: 1;
        left: 2em;
        background-color: white;
        padding: 0 5px;
    }

    select {
        height: 6ex;
    }

    #wrapper {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;

        display: flex;
        align-items: center;
        flex-direction: column;

    }

    #formall {
        display: flex;
        flex-direction: column;
        position: relative;
        width: 50%;
        min-height: 100px;
        border: 1px solid #ECECEC;
        padding: 2.5%;
        top: 100px;
    }

    #formheader {
        position: relative;
        height: 100px;
        width: 100%;
    }

    #formheader a {
        position: absolute;
        bottom: 0;
        right: 0;
        margin: 10px;
    }

    #formcontent {
        display: none;
        flex-direction: column;
    }

    form {
        position: relative;

        display: flex;
        flex-direction: column;
    }

    .hero {
        position: relative;
        width: 100%;
        height: 200px;
        background-color: black;
    }

    .hero img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.9;
    }

    .herotext {
        position: absolute;
        transform: translate(-50%, -50%);
        top: 50%;
        left: 30%;
        font-size: 4rem;
        color: white;
        font-weight: bold;
    }   



    /*Button thingy*/
    .button {
        display: flex;
        height: 50px;
        padding: 0;
        background: #009578;
        border: none;
        outline: none;
        border-radius: 5px;
        overflow: hidden;
        font-family: "Quicksand", sans-serif;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;

        margin-left: 10px;


        /*Prevents stretching*/
        align-self: start; 
    }

    .button:hover {
        background: #008168;
    }

    .button:active {
        background: #006e58;
    }

    .button__text,
    .button__icon {
        display: inline-flex;
        align-items: center;
        padding: 0 24px;
        color: #fff;
        height: 100%;
    }

    .button__icon {
        font-size: 1.5em;
        background: rgba(0, 0, 0, 0.08);
    }

    /* endbutton */

    .formposts {
        position: relative;
    }
    
    .post__element {
        padding-bottom: 10px;
    }

    .post__header, .post__element {
        display: flex;

        flex-direction: row;

        width: 100%;

    }


    .post__header h3, .post__element p {
        width: 20%;
    }


    .post__element button {
        background: #BD5151;
    }


    .post__element button:hover {
        background: #C95757;
    }

    .post__element button:active {
        background: #FD6E6E
    }

    .formposts h2 {
        padding-bottom: 5px;
        padding-top: 5px;
    }

    @media screen and (max-width: 1600px) {
        #formall {
            width: 65%;
        }
    }

    @media screen and (max-width: 1200px) {
        #formall {
            width: 75%;
        }
    }

    @media screen and (max-width: 1000px) {
        #formall {
            width: 95% !important;
            padding: 20px !important;
            top: 20px !important;
        }

        #formheader {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
        }

        #formheader a {
            margin: 0 !important;
            position: relative !important;
        }

        form {
            align-items: center !important;
        }

        form button {
            align-self: auto !important;
        }

        #formcontent h3 {
            align-self: center !important;
        }

        form input, form select{
            margin-left: 0 !important;
        }

        #formcontent {
            flex-direction: column !important;
        }

        .hero {
        position: relative !important;
        width: 100% !important;
        height: 200px !important;
        background-color: black !important;
        }

        .herotext {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important ;

            transform: initial !important;
            position: absolute !important;
            top: 0% !important;
            left: 0% !important;
            font-size: 4rem !important;
            color: white !important;
            font-weight: bold !important;
            height: 100% !important;
            width: 100% !important;
        }
        .herotext p {
            margin: 0;
            text-align: center !important;
            font-size: 3rem !important;
        }
        
        .button__text {
            display: none !important;
        }

        .post__element {
            justify-content: center !important;
        }

        .post__element .button {
            margin-left: 15px !important;
        }

        .button__icon {
            padding: 0 15px !important;
        }

        .formposts h2 {
            text-align: center !important;
        }

        .post__header {
            margin-left: 15px !important;
        }
    }
    </style>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Collection</title>
</head>
    <body>

        <div id="wrapper">
            <div class="hero">
                <img src="https://www.collinsdictionary.com/images/full/concert_295115348.jpg"/>
                <div class="herotext">
                    <p>Music Collection</p>
                </div>
            </div>

            <div id="formall" >
                <div id="formheader">
                    <h2>Add Something to your collection!</h2>
                    <a onclick="extendadd()">Submit a new song </a>
                </div>

                <div id="formcontent">
                    <hr>
                    <h3>Submit a new song</h3>
                    <form action="music.php" method="post">
                        <div class="input-wrapper">
                            <label for="artist">Artist</label>
                            <input type="text" name="artist" required>
                        </div>

                        <div class="input-wrapper">
                        <label for="album">Album</label>
                        <input type="text" name="album" required>
                        </div>

                        <div class="input-wrapper">
                        <label for="datum">Date</label>
                        <input type="date" name="datum" required>
                        </div>

                        <div class="input-wrapper">
                        <label for="artist">Type</label>
                        <select name="typ" required>
                            <option value="">Choose</option>
                            <option value="c">CD</option>
                            <option value="k">Kasette</option>
                            <option value="v">Vinyl (Schallplatte)</option>
                            <option value="d">Digital</option>
                        </select>
                        </div>

                        <button type="submit" name="insert" value="new" type="button" class="button">
                            <span class="button__text">Add</span>
                                <span class="button__icon">
                                    <ion-icon name="add-outline"></ion-icon>
                            </span>
                        </button>
                    </form>
                </div>
                <div class="formposts">
                    <hr>
                    <h2>Music Posts</h2>
                    <div class="post__header">
                        <h3>Artist</h3>
                        <h3>Album</h3>
                        <h3>Date</h3>
                        <h3>Type</h3>
                    </div>
                    <?php 
                        create_elements($conn);
                    ?>
                    <!--
                    <div class="post__element">
                        <p>Micheal Jackson</p>
                        <p>Thriller</p>
                        <p>29.11.1982</p>
                        <p>CD</p>
                        <button type="submit" name="insert" value="delete" type="button" class="button">
                            <span class="button__text">Delete</span>
                                <span class="button__icon">
                                    <ion-icon name="trash-outline"></ion-icon>
                            </span>
                        </button>
                    </div>
                    <div class="post__element">
                        <p>Micheal Jackson</p>
                        <p>Thriller</p>
                        <p>29.11.1982</p>
                        <p>CD</p>
                        <button type="submit" name="insert" value="delete" type="button" class="button">
                            <span class="button__text">Delete</span>
                                <span class="button__icon">
                                    <ion-icon name="trash-outline"></ion-icon>
                            </span>
                        </button>
                    </div>
                    <div class="post__element">
                        <p>Micheal Jackson</p>
                        <p>Thriller</p>
                        <p>29.11.1982</p>
                        <p>CD</p>
                        <button type="submit" name="insert" value="delete" type="button" class="button">
                            <span class="button__text">Delete</span>
                                <span class="button__icon">
                                    <ion-icon name="trash-outline"></ion-icon>
                            </span>
                        </button>
                    </div>
                -->
                </div>
            </div>
            <div id="musiclist"></div>
        </div>
    </body>
</html>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script>
    function extendadd() {
        let form = document.getElementById("formcontent");

        if (form.style.display == "flex") {
            document.getElementById("formcontent").style.display = "none";
        } else {
            document.getElementById("formcontent").style.display = "flex";
        }
    }

</script>