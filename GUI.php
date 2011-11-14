<?php

class GUI {
    private $conf;
    private $user;
    private $auth;

    public function __construct($conf,$user,$auth){
        $this->conf = $conf;
        $this->user = $user;
        $this->auth = $auth;
    }

    public function header(){
    ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <link href="fileuploader.css" rel="stylesheet" type="text/css">
            <link href="style.css" rel="stylesheet" type="text/css">
            <style>
                body {font-size:13px; font-family:arial, sans-serif; width:700px; margin:100px auto;}
            </style>
        </head>
        <body>
        <ul class="tabs">
            <li><a href=".">Download</a></li>
            <li><a href="upload">Upload</a></li>
            <?php
            if($this->user == 'admin') echo '<li><a href="userlist">Users</a></li>';
            ?>
        </ul>
        <div class="wrap">
    <?php
    }

    public function footer(){
    ?>
        </div>
        </body>
        </html>
    <?php
    }

    public function uploadform(){
    ?>
        <p>To upload a file, click on the button below. Drag-and-drop is supported in FF, Chrome.</p>
        <div id="file-uploader">
            <noscript>
                <p>Please enable JavaScript to use file uploader.</p>
                <!-- or put a simple form for upload here FIXME -->
            </noscript>
        </div>

        <script src="fileuploader.js" type="text/javascript"></script>
        <script>
            function createUploader(){
                var uploader = new qq.FileUploader({
                    element: document.getElementById('file-uploader'),
                    action: 'up',
                    debug: true
                });
            }

            // in your app create uploader as soon as the DOM is ready
            // don't wait for the window to load FIXME
            window.onload = createUploader;
        </script>
    <?php
    }

    public function filelist(){
        $files = glob($this->conf['uploaddir'].'/'.$this->user.'/*');
        if(!count($files)){
            echo '<p>No files uploaded, yet</p>';
            return;
        }
        sort($files);
        echo '<table class="filelist">';
        echo '<tr>';
        echo '<th>File</th>';
        echo '<th>Size</th>';
        echo '<th>Uploaded at</th>';
        echo '</tr>';
        foreach($files as $file){
            $name = basename($file);
            echo '<tr>';

            echo '<td>';
            echo '<a href="download?file='.htmlspecialchars($name).'">';
            echo htmlspecialchars($name);
            echo '</a>';
            echo '</td>';

            echo '<td>';
            echo filesize($file);
            echo '</td>';

            echo '<td>';
            echo strftime('%Y-%m-%d %H:%M',filemtime($file));
            echo '</td>';

            echo '</tr>';
        }
        echo '</table>';
    }

    public function userlist(){
        $users = $this->auth->users;
        ksort($users);

        echo '<table class="filelist">';
        echo '<tr>';
        echo '<th>User</th>';
        echo '<th>Pass</th>';
        echo '</tr>';
        foreach($users as $user => $info){
            echo '<tr>';

            echo '<td>';
            echo htmlspecialchars($user);
            echo '</td>';

            echo '<td>';
            echo '<form action="userlist" method="post">';
            echo '<input type="hidden" name="do"   value="useredit">';
            echo '<input type="hidden" name="user" value="'.htmlspecialchars($user).'">';
            echo '<input type="text"   name="info[pass]" value="'.htmlspecialchars($info['pass']).'" />';
            echo '<input type="submit" value="save" />';
            echo '</form>';
            echo '</td>';

            echo '</tr>';
        }

        echo '<tr>';
        echo '<form action="userlist" method="post">';
        echo '<input type="hidden" name="do"   value="useredit">';

        echo '<td>';
        echo '<input type="text" name="user" value="">';
        echo '</td>';

        echo '<td>';
        echo '<input type="text" name="info[pass]" />';
        echo '<input type="submit" value="save" />';
        echo '</td>';

        echo '</form>';
        echo '</tr>';
        echo '</table>';
    }
}
