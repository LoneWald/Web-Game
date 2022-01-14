<div class="header">
    <div class="inner-header">
        <div class="logo-container">
            <a href="index.php">
                <h1>Морской<span>Бой</span></h1>
            </a>
        </div>
        <ul class="navigation">
            <?php 
            $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $url = explode('?', $url);
            $result = $url[0];
            if($result != "https://web-game/game.php" && $result != "https://web-game/prepareToBattle.php"){
                if(isset($_SESSION['game'])){
                ?>
            <li>
                <div>
                    <a href="game.php">Вернуться к бою</a>
                </div>
            </li>
            <?php
            } 
            else 
            {
                ?>
            <li>
                <div>
                    <a href="prepareToBattle.php">В бой</a>
                </div>
            </li>
            <?php
            }
        }
            ?>
            <li>
                <div>
                    <a>Списки лидеров</a>
                </div>
            </li>
            <?php 
            if($_COOKIE["login"] != null && $_COOKIE["password"] != null){
                ?>
            <li>
                <div class="dropdown">
                    <a class="dropbtn">Аккаунт</a>
                    <div class="dropdown-content">
                        <div><a href="account.php">Моя Страница</a></div>
                        <div><a>Друзья</a></div>
                        <div><a href="logout.php">Выйти</a></div>
                    </div>
                </div>
            </li>
            <?php
            } 
            else 
            {
                ?>
            <li>
                <div>
                    <a href="login.php">Войти</a>
                </div>
            </li>
            <?php
            }
            ?>
        </ul>
    </div>
</div>