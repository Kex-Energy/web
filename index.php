<HTML>
    <HEAD>
        <meta charset="utf-8">
        <TITLE> Главная </TITLE>
        <link href="style.css" rel="stylesheet">
        <script src="ckeditor\ckeditor.js"></script>
    </HEAD>
    <BODY>
        <form method = "post" action = "index.php">
        <div id = "Hat">
        <div id = "main_ref"> <input type = "submit" value = "Главная" name = "main_ref" class = "hat_buttons"> </div> <div class = "right_text"> <?php if(isset($_COOKIE['user'])){ echo "<input type = 'submit' value = 'Выход' name = 'logout' class = 'hat_buttons'>"; }else{ echo "<input type = 'submit' value = 'Логин' name = 'login' class = 'hat_buttons'> <input type = 'submit' value = 'Регистрация' name = 'register' class = 'hat_buttons'>"; } ?> </div> 
        </div>
            <div id = "main_window"> <?php
                include("includes/connection.php");
                if (isset($_POST['login']))
                {
                    setcookie('lastname','');
                    setcookie('lastarticle', '');
                    setcookie("location", "login_page");
                    header("Location: index.php");
                }
                else
                if (isset($_POST['register']))
                {
                    setcookie('lastname','');
                    setcookie('lastarticle', '');
                    setcookie("location", "reg_page");
                    header("Location: index.php");
                }
                else
                if (isset($_POST['main_ref']) || !isset($_COOKIE['location']))
                {
                    setcookie('lastname','');
                    setcookie('lastarticle', '');
                    setcookie("location", "main_page");
                    header("Location: index.php");
                }
                else
                if(isset($_POST['logout']))
                {
                    setcookie('user','');
                    setcookie('article_id','');
                    setcookie('lastname','');
                    setcookie('lastarticle', '');
                    header("Location: index.php");
                }
                else
                if(isset($_POST['check_register']))
                {
                    if(!empty($_POST['username']) && !empty($_POST['password']))
                    {
                        if(strlen($_POST['username']) >= 5 && strlen($_POST['username']) <= 25)
                        {
                            if(strlen($_POST['password']) >= 5 && strlen($_POST['password']) <= 15)
                            {
                                $result = $con->query("SELECT COUNT(*) FROM users WHERE username='".$_POST['username']."'");
                                $table = $result->fetch(PDO::FETCH_ASSOC);
                                if($table['COUNT(*)'] == 0)
                                {
                                    $password = md5($_POST['password']);
                                    $result = $con->query("INSERT INTO users (username, password) VALUES ('".$_POST['username']."','".$password."')");
                                    setcookie("message","Вы успешно зарегестрированы");
                                    setcookie("location", "login_page");
                                }
                                else
                                {
                                    setcookie("message","Данный пользователь уже сушествует");
                                }
                            }
                            else
                            {
                                setcookie("message","Пароль должен быть в пределах от 5 до 15 символов");
                            }
                        }
                        else
                        {
                            setcookie("message","Имя пользователя должно быть в пределах от 5 до 25 символов");
                        }
                    }
                    else
                    {
                        setcookie("message","Заполните все поля");
                    }
                    header("Location: index.php");
                }
                else
                if(isset($_POST['check_login']))
                {
                    $result = $con->query("SELECT * FROM users WHERE username='".$_POST['username']."'");
                    $table = $result->fetch(PDO::FETCH_ASSOC);
                    $password = md5($_POST['password']);
                    if($table['username'] == $_POST['username'] && $table['password'] == $password)
                    {
                        setcookie('user',$_POST["username"]);
                        setcookie("location", "main_page");
                    }
                    else
                    {
                        setcookie("message","Неверные данные входа");
                    }
                    header("Location: index.php");
                }
                else
                if(isset($_POST['create_ref']) && isset($_COOKIE["user"]))
                {
                    setcookie('lastname','');
                    setcookie('lastarticle', '');
                    setcookie("location", "create_page");
                    header("Location: index.php");
                }
                else
                if(isset($_POST['create_article']) && isset($_COOKIE["user"]))
                {
                    if(!empty($_POST['name']) && !empty($_POST['editor']))
                    {
                        $con->query("INSERT INTO articles (name, article, author) VALUES ('".$_POST['name']."','".$_POST['editor']."','".$_COOKIE['user']."')");
                        setcookie('message','Статья успешно добавлена');
                    }
                    else
                    {
                        setcookie('lastname',$_POST['name']);
                        setcookie('message','Проверьте заполненность полей');
                    }
                    header("Location: index.php");
                }
                else
                if(isset($_POST['view_article']))
                {
                    setcookie('lastname','');
                    setcookie('lastarticle', '');
                    $number = substr($_POST['view_article'],0,1);
                    $result = $con->query("SELECT COUNT(*) FROM articles");
                    $table = $result->fetch(PDO::FETCH_ASSOC);
                    if($table['COUNT(*)'] >= $number)
                    {
                    setcookie("article_number", $number);
                    setcookie("location", "article");
                    }
                    header("Location: index.php");
                    

                }
                else
                if(isset($_POST['update_article']) && isset($_COOKIE['user']))
                {
                    $result = $con->query("SELECT * FROM articles WHERE id='".$_COOKIE['article_id']."'");
                    $table = $result->fetch(PDO::FETCH_ASSOC);
                    setcookie('lastname', $table['name']);
                    setcookie('lastarticle', 1);
                    setcookie("location", "update");
                    header("Location: index.php");
                }
                else
                if(isset($_POST['delete_article']) && isset($_COOKIE['user']))
                {
                    $result = $con->query("SELECT * FROM articles");
                    for($i = 1; $i <= $_COOKIE['article_number']; $i++)
                    {
                        $table = $result->fetch(PDO::FETCH_ASSOC);
                    }
                    $con->query("DELETE FROM articles WHERE id='".$table['id']."'");
                    setcookie("location", "main_page");
                    header("Location: index.php");
                }
                else
                if(isset($_POST['update_end']))
                {
                    if(!empty($_POST['name']) && !empty($_POST['editor']))
                    {
                        $con->query("UPDATE articles SET name = '".$_POST['name']."' , article = '".$_POST['editor']."' , author = '".$_COOKIE['user']."' WHERE id = '".$_COOKIE['article_id']."'");
                        setcookie('message','Статья успешно обновлена');
                    }
                    else
                    {
                        setcookie('message','Проверьте заполненность полей');
                    }
                    setcookie('lastname',$_POST['name']); 
                    setcookie('lastarticle', $_POST['editor']);
                    header("Location: index.php");
                }


                

                
                
                if ($_COOKIE["location"] == "login_page")
                {
                    
                    echo "Вход на сайт <br>";
                    echo "Имя пользователя <br> <input type = 'text' name = 'username' class = 'login_textbox'>";
                    echo "<br> Пароль <br> <input type = 'password' name = 'password' class = 'login_textbox'>";
                    echo "<br> <input type = 'submit' name = 'check_login' value = 'Войти' class = 'login_textbox'><br>";
                    if(isset($_COOKIE["message"]))
                    {
                    print_r($_COOKIE["message"]);
                    setcookie("message","");
                    }
                }
                else
                if ($_COOKIE["location"] == "reg_page")
                {
                    echo "Регистрация <br>";
                    echo "Имя пользователя <br> <input type = 'text' name = 'username' class = 'login_textbox'>";
                    echo "<br> Пароль <br> <input type = 'password' name = 'password' class = 'login_textbox'>";
                    echo "<br> <input type = 'submit' name = 'check_register' value = 'Регистрация' class = 'login_textbox'><br>";
                    if(isset($_COOKIE["message"]))
                    {
                    print_r($_COOKIE["message"]);
                    setcookie("message","");
                    }
                    
                }
                else
                if(($_COOKIE["location"] == "create_page" || $_COOKIE["location"] == "update") && isset($_COOKIE["user"]))
                {
                    echo "Название статьи <br><input type = 'text' name = 'name' class = 'login_textbox' ";
                    if(isset($_COOKIE['lastname'])) { echo "value = '".$_COOKIE['lastname'];}
                    echo "'><br>";
                    echo "<textarea name = 'editor' id = 'editor' rows='10' cols='80'>";
                    if(isset($_COOKIE['lastarticle']) && $_COOKIE["location"] == "update") {
                        $result = $con->query("SELECT * FROM articles WHERE id='".$_COOKIE['article_id']."'");
                        $table = $result->fetch(PDO::FETCH_ASSOC);
                        echo $table['article'];
                    }
                    echo "</textarea>";
                    if($_COOKIE["location"] == "update")
                    {
                        echo "<br> <input type = 'submit' name = 'update_end' value = 'Изменить статью' class = 'login_textbox'>";

                    }
                    else
                        echo "<br> <input type = 'submit' name = 'create_article' value = 'Добавить статью' class = 'login_textbox'>";
                    if(!empty($_COOKIE["message"]))
                    {
                    print_r($_COOKIE["message"]);
                    setcookie("message","");
                    }
                }
                else
                if($_COOKIE["location"] == "article")
                {
                    $result = $con->query("SELECT * FROM articles");
                    for($i = 1; $i <= $_COOKIE['article_number']; $i++)
                    {
                        $table = $result->fetch(PDO::FETCH_ASSOC);
                    }
                    setcookie("article_id", $table['id']);
                    echo "<h3>".$table['name']."</h3>";
                    echo $table['article'];
                    echo "<p>Автор:".$table['author'];
                    if(isset($_COOKIE["user"]))
                    {
                    echo "<br> <input type = 'submit' name = 'update_article' value = 'Изменить статью' class = 'login_textbox'> <input type = 'submit' name = 'delete_article' value = 'Удалить статью' class = 'login_textbox'>";
                    }
                }
                else
                if (($_COOKIE["location"] == "main_page") || (!isset($_COOKIE["user"])))
                {
                    echo "<h3>Что такое SQLite?</h3>

                    <p>SQLite - это встраиваемая библиотека в которой реализовано многое из<br>
                    стандарта SQL 92. Её притязанием на известность является как<br>
                    собственно сам движок базы, так и её интерфейс (точнее его движок) в<br>
                    пределах одной библиотеки, а также возможность хранить все данные в<br>
                    одном файле. Я отношу позицию функциональности SQLite где-то между<br>
                    MySQL и PostgreSQL. Однако, на практике, SQLite не редко оказывается в<br>
                    2-3 раза (и даже больше) быстрее. Такое возможно благодаря<br>
                    высокоупорядоченной внутренней архитектуре и устранению необходимости<br>
                    в соединениях типа <<сервер-клиент>> и <<клиент-сервер>>.</p>
                 
                    <p>Всё это, собранное в один пакет, лишь немногим больше по размеру<br>
                    клиентской части библиотеки MySQL, является впечатляющим достижением<br>
                    для полноценной базы данных. Используя высоко эффективную<br>
                    инфраструктуру, SQLite может работать в крошечном объёме выделяемой<br>
                    для неё памяти, гораздо меньшем, чем в любых других системах БД. Это<br>
                    делает SQLite очень удобным инструментом с возможностью использования<br>
                    практически в любых задачах возлагаемых на базу данных.</p>";
                }
                
                

                ?></div>
        <div id=article_block>
            <div id = "article_button"> <input type = "submit" value = "Создание статьи" name = "create_ref" id = "create_button" <?php if(!isset($_COOKIE["user"])) echo "disabled"; ?> > </div>
            <div id = "article_list"> 
                <?php
                $number = 1;
                $result = $con->query("SELECT * FROM articles");
                while($table = $result->fetch(PDO::FETCH_ASSOC))
                {
                    echo "<input type = 'submit' name = 'view_article' value = '".$number.".".$table['name']."' class = 'view_buttons'><br>";
                    $number++;
                }
                ?>
            </div>
        </div>
        </form>

    
    <script>
	CKEDITOR.replace( 'editor' );
    
    </script>
    </BODY>
</HTML>