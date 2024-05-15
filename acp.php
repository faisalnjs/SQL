<?php
if (isset($_POST['json'])) {
    if (!empty($_POST["username"]) && !empty($_POST["password"]) && !empty($_POST["database"]) && !empty($_POST["query"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];
        $hostname = "da.dangoweb.com";
        $database = $_POST["database"];
        $query = $_POST["query"];
        try {
            $dbhandle = mysqli_connect($hostname, $username, $password, $database);
        } catch (exception $e) {
            if ($e) {
                if (strpos($e->getMessage(), 'Access denied for user ') !== false) {
                    echo json_encode(array("error" => "true", "message" => "Access denied for user '" . $username . "'. Please check your username and password."));
                } else {
                    echo json_encode(array("error" => "true", "message" => "Failed to connect to MySQL: " . $e->getMessage()));
                }
                exit();
            }
        }
        try {
            $result = mysqli_query($dbhandle, $_POST["query"]);
            if ($result === true) {
                echo json_encode(array("error" => "false", "message" => "Query executed successfully!"));
            } else if ($result === false) {
                echo json_encode(array("error" => "true", "message" => "Query execution failed!"));
            } else {
                $columns = mysqli_fetch_fields($result);
                $data = array();
                while ($row = mysqli_fetch_assoc($result)) {
                    $record = array();
                    foreach ($columns as $column) {
                        $record[$column->name] = $row[$column->name];
                    }
                    $data[] = $record;
                }
                echo json_encode(array("error" => "true", "message" => $data));
            }
        } catch (exception $e) {
            echo json_encode(array("error" => "true", "message" => "Failed to execute query: " . $e->getMessage()));
        }
        mysqli_close($dbhandle);
    } else {
        echo json_encode(array("error" => "true", "message" => "Fill in all parameters!"));
    }
} else {
?>
    <html>

    <head>
        <title>sql @ dangoweb.com</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            html {
                margin: 1%;
                scroll-behavior: smooth;
            }

            html::-webkit-scrollbar {
                display: none;
            }

            body {
                --background: white;
                --text: black;
                padding: 5%;
                margin: 0;
                background: var(--background);
                color: var(--text);
                font-family: system-ui;
            }

            body.dark {
                --background: rgb(24 24 24);
                --text: white;
            }

            body.hacker {
                --background: black;
                --text: lime;
            }

            * {
                color: var(--text);
                font-family: system-ui;
            }

            p {
                font-size: 15px;
                width: clamp(300px, 50%, 700px);
                margin-bottom: 25px;
            }

            .section {
                display: flex;
                flex-direction: column;
                gap: 10px;
                margin: 25px 0;
            }

            .section header {
                text-align: center;
            }

            .section>div {
                display: flex;
                flex-direction: column;
            }

            .section>div:has(code) {
                flex-direction: row;
                align-items: center;
                gap: 10px;
            }

            .section>div:has(code) code {
                margin-bottom: -5px;
            }

            .section>div:not(:has(code)) {
                margin: 5px 0;
            }

            input {
                background: transparent;
                border: none;
                border-bottom: 2px solid;
                padding: 10px 10px 10px 0;
                font-family: monospace;
                text-decoration-line: none;
            }

            input[type="submit"] {
                background: var(--text);
                color: var(--background) !important;
                border: none;
                cursor: pointer;
            }

            input[type="submit"]:hover {
                opacity: 0.8;
            }

            table * {
                text-align: left;
            }

            tr:nth-child(even) {
                backdrop-filter: contrast(0.5);
            }

            .body.hacker tr:nth-child(even) {
                background: #00ff0030;
            }

            code {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                font-family: monospace;
            }

            code * {
                font-family: monospace;
            }

            tab {
                margin-left: 15px;
            }

            button {
                width: 100%;
                padding: 10px 15px;
                background: var(--text);
                border: none;
                text-align: left;
                color: var(--background);
                cursor: pointer;
                font-weight: 900;
            }

            .intro,
            .hints,
            .dataTypes {
                height: 0px;
                overflow: hidden;
                transition: 0.5s;
            }

            .intro.active,
            .hints.active,
            .dataTypes.active {
                height: 100%;
                overflow-y: scroll;
            }

            @media screen and (max-width: 900px) {
                .hints>* {
                    flex-direction: column !important;
                    align-items: flex-start !important;
                }
            }
        </style>
    </head>

    <body>
        <h2>sql @ dangoweb.com</h2>
        <button type="button" onclick="document.querySelector('.intro').classList.toggle('active')">Toggle intro</button>
        <div class="intro">
            <p>Learn how to connect to a SQL server externally using vanilla JavaScript. To begin, enter your database login details you have created. Most often your database name will start with 'username_' if your username is 'username'. Then, craft your SQL query to use the database. You can use SELECT, INSERT, UPDATE, DELETE, and more functions. I have added buttons for pre-filling the query if you need help.</p>
            <p>To log into the database panel directly to view and edit your data, go to <a href="https://da.dangoweb.com/phpmyadmin">phpMyAdmin</a>. If you haven't already made a database for yourself, create one in the <a href="https://da.dangoweb.com:2222/user/database">Web Hosting panel here</a>.</p>
            <p>An example of working connection details is below. It connects to the database on server with correct credentials, executes a correct query, and returns the requested data.</p>
            <img src="example.png" width="200" />
            <video controls style="width: 500px;">
                <source src="example.mp4" type="video/mp4">
            </video>
        </div>
        <form method="post" action="#query">
            <div class="section">
                <header>--- connection ---</header>
                <div>Username: <input type="text" name="username" value="<?php echo isset($_POST["username"]) ? $_POST["username"] : "" ?>" required></div>
                <div>Password: <input type="password" name="password" value="<?php echo isset($_POST["password"]) ? $_POST["password"] : "" ?>" required></div>
                <div>Database: <input type="text" name="database" value="<?php echo isset($_POST["database"]) ? $_POST["database"] : "" ?>" required></div>
                <div>Server: <?php echo $_SERVER['SERVER_NAME'] ?></div>
            </div>
            <button type="button" onclick="document.querySelector('.hints').classList.toggle('active')">Toggle hints</button>
            <div class="section hints">
                <div>Select One Row: <code onclick="copy()">SELECT * FROM tableName WHERE columnName = 'hello' LIMIT 1</code></div>
                <div>Select Many Rows: <code onclick="copy()">SELECT * FROM tableName WHERE columnName = 'hello'</code></div>
                <div>Select All Rows: <code onclick="copy()">SELECT * FROM tableName</code></div>
                <div>Insert One Row: <code onclick="copy()">INSERT INTO tableName(`columnName`) VALUES ('hello')</code></div>
                <div>Insert Many Rows: <code onclick="copy()">INSERT INTO tableName(`columnName`, `anotherColumn`) VALUES ('hello'), ('hello world')</code></div>
                <div>Update One Row: <code onclick="copy()">UPDATE tableName SET `columnName` = 'world' WHERE `columnName` = 'hello' LIMIT 1</code></div>
                <div>Update Many Rows: <code onclick="copy()">UPDATE tableName SET `columnName` = 'world' WHERE `columnName` = 'hello'</code></div>
                <div>Update Multiple Columns: <code onclick="copy()">UPDATE tableName SET `columnName` = 'world', `columnName2` = 'hello' WHERE `columnName` = 'hello'</code></div>
                <div>Update All Rows: <code onclick="copy()">UPDATE tableName SET `columnName` = 'world'</code></div>
                <div>Delete One Row: <code onclick="copy()">DELETE FROM tableName WHERE `columnName` = 'hello' LIMIT 1</code></div>
                <div>Delete Many Rows: <code onclick="copy()">DELETE FROM tableName WHERE `columnName` = 'hello'</code></div>
                <div>Delete All Rows: <code onclick="copy()">DELETE FROM tableName</code></div>
                <br>
                <br>
                <div>Rows to return: <code onclick="copy()">LIMIT 1</code></div>
                <div>Includes value: <code onclick="copy()">WHERE `columnName` LIKE '%hello%'</code></div>
                <div>And condition: <code onclick="copy()">WHERE `columnName` = 'hello' AND WHERE `columnName2` = `world`</code></div>
                <div>Or condition: <code onclick="copy()">WHERE `columnName` = 'hello' OR WHERE `columnName2` = `world`</code></div>
                <div>Not condition: <code onclick="copy()">WHERE NOT `columnName` = 'hello'</code></div>
                <br>
                <br>
                <div>Create table: <code onclick="copy()">CREATE TABLE tableName (columnName VARCHAR(255), columnName2 INT)</code></div>
            </div>
            <button type="button" onclick="document.querySelector('.dataTypes').classList.toggle('active')">Toggle data types</button>
            <div class="section dataTypes">
                <img src="dataTypes.png">
            </div>
            <div class="section" id="query">
                <header>--- query ---</header>
                <div>Query: <input type="text" name="query" onkeyup="fixQuery()" value="<?php echo isset($_POST["query"]) ? $_POST["query"] : "" ?>" required></div>
            </div>
            <div class="section">
                <header>--- test ---</header>
                <div><input type="submit" value="GO" name="Submit1"></div>
            </div>
        </form>
        <div class="section">
            <header>--- result---</header>
            <?php
            if (isset($_POST['Submit1'])) {
                if (!empty($_POST["username"]) && !empty($_POST["password"]) && !empty($_POST["database"]) && !empty($_POST["query"])) {
                    $username = $_POST["username"];
                    $password = $_POST["password"];
                    $hostname = "da.dangoweb.com";
                    $database = $_POST["database"];
                    $query = $_POST["query"];
                    try {
                        $dbhandle = mysqli_connect($hostname, $username, $password, $database);
                    } catch (exception $e) {
                        if ($e) {
                            if (strpos($e->getMessage(), 'Access denied for user ') !== false) {
                                echo "<div class='alert error'>Access denied for user '" . $username . "'. Please check your username and password.</div>";
                            } else {
                                echo "<div class='alert error'>Failed to connect to MySQL: " . $e->getMessage() . "</div>";
                            }
                            exit();
                        }
                    }
                    try {
                        $result = mysqli_query($dbhandle, $_POST["query"]);
                        if ($result === true) {
                            echo "<div class='alert success'>Query executed successfully!</div><br>";
                        } else if ($result === false) {
                            echo "<div class='alert error'>Query execution failed!</div><br>";
                        } else {
                            echo "<table>";
                            $columns = mysqli_fetch_fields($result);
                            echo "<tr>";
                            foreach ($columns as $column) {
                                echo "<th>" . $column->name . "</th>";
                            }
                            echo "</tr>";
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                foreach ($columns as $column) {
                                    echo "<td>" . $row[$column->name] . "</td>";
                                }
                                echo "</tr>";
                            }
                            echo "</table>";
                        }
                        echo "</div><div class='section'><header>--- request ---</header>";
                        echo "<div>Type: POST</div>";
                        echo "<div>URL: http://$_SERVER[SERVER_NAME]$_SERVER[REQUEST_URI]</div></div>";
                        echo "<div class='section'><header>--- example ---</header>";
                        $script = <<<EOT
                    <code onclick="copy()">
                        <tab>fetch('//$_SERVER[SERVER_NAME]$_SERVER[REQUEST_URI]', {</tab>
                        <tab><tab>method: 'POST',</tab></tab>
                        <tab><tab>headers: {</tab></tab>
                        <tab><tab><tab></tab>'Content-Type': 'application/x-www-form-urlencoded',</tab></tab>
                        <tab><tab>},</tab></tab>
                        <tab><tab>body: new URLSearchParams({</tab></tab>
                        <tab><tab><tab>'username': '$username', // username</tab></tab></tab>
                        <tab><tab><tab>'password': '$password', // password</tab></tab></tab>
                        <tab><tab><tab>'database': '$database', // database</tab></tab></tab>
                        <tab><tab><tab>'query': `$query`, // query</tab></tab></tab>
                        <tab><tab><tab>'json': 'true'</tab></tab></tab>
                        <tab><tab>})</tab></tab>
                        <tab>})</tab>
                        <tab>.then(response => response.json()) // data as JSON</tab>
                        <tab>.then(data => {</tab>
                        <tab><tab>if (data.error && data.error === true) alert(data.message); // alert if error</tab></tab>
                        <tab><tab>try {</tab></tab>
                        <tab><tab><tab>data.message.forEach(row => alert(row.columnName)); // display each row</tab></tab></tab>
                        <tab><tab>} catch { }; // returns nothing if success but no data returned</tab></tab>
                        <tab>})</tab>
                        <tab>.catch((error) => { // catch error</tab>
                        <tab><tab>alert('Error:', error); // alert if error with code</tab></tab>
                        <tab>});</tab>
                    </code>
                    EOT;

                        echo $script . "</div>";
                    } catch (exception $e) {
                        echo "<div class='alert error'>Failed to execute query: " . $e->getMessage() . "</div>";
                    }
                    mysqli_close($dbhandle);
                } else {
                    echo "<div class='alert error'>Fill in all parameters!</div>";
                }
            }
            ?>
        </div>

        <p style="margin: 10px 0 -20px 0">&copy; <?php echo date("Y") ?> Faisal N | Hosted @ <a href="https://dangoweb.com">dangoweb.com</a> | <a href="https://github.com/faisalnjs/sql">Open Source @ GitHub</a></p>
        <br />
        <br />
        <button type="button" onclick="toggleThemes()" style="margin: 0 0 -50px 0">Toggle theme</button>
    </body>
    <script>
        function copy(event) {
            function selectText(node) {
                if (document.body.createTextRange) {
                    const range = document.body.createTextRange();
                    range.moveToElementText(node);
                    range.select();
                } else if (window.getSelection) {
                    const selection = window.getSelection();
                    const range = document.createRange();
                    range.selectNodeContents(node);
                    selection.removeAllRanges();
                    selection.addRange(range);
                }
            };
            selectText(event.target);
            document.execCommand('copy');
            alert('Copied to clipboard!');
            document.getSelection().removeAllRanges();
        };

        document.querySelectorAll('code').forEach(codeElement => {
            codeElement.addEventListener('click', copy);
        });

        const themes = ['dark', 'hacker', ''];

        function toggleThemes() {
            const body = document.querySelector('body');
            const currentTheme = body.classList[0];
            const nextTheme = themes[(themes.indexOf(currentTheme) + 1) % themes.length];
            body.classList.remove(currentTheme);
            body.classList.add(nextTheme);
            localStorage.setItem('theme', nextTheme);
        };

        window.onload = function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                const body = document.querySelector('body');
                body.classList.add(savedTheme);
            };
        };

        function fixQuery() {
            elem = document.querySelector('input');
            elem.value = elem.value.replaceAll('select', 'SELECT').replaceAll('from', 'FROM').replaceAll('where', 'WHERE').replaceAll('limit', 'LIMIT').replaceAll('insert', 'INSERT').replaceAll('into', 'INTO').replaceAll('values', 'VALUES').replaceAll('update', 'UPDATE').replaceAll('set', 'SET').replaceAll('delete', 'DELETE').replaceAll('like', 'LIKE').replaceAll('and', 'AND').replaceAll('or', 'OR').replaceAll('not', 'NOT').replaceAll('create', 'CREATE');
        };
    </script>

    </html>
<?php
}
