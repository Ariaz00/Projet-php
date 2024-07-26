<!-- Réalisez un Labyrinthe !
Le joueur contrôle un chat qui doit se déplacer dans un labyrinthe jusqu’à trouver
la souris. Pour cela, vous aurez quelques règles à respecter :
➢ Le chat doit se déplacer grâce à 4 boutons (haut, bas, gauche, droite) qui le
feront bouger de 1 case
➢ Un brouillard de guerre est dispersé dans tout le labyrinthe, seules les 4 cases
autour du chat sont visibles
➢ Des murs sont positionnés dans le labyrinthe, vous ne pourrez pas vous
déplacer vers eux ni vous déplacer en dehors des cases du labyrinthe
➢ Vous créerez au moins 2 labyrinthes différents, le labyrinthe sera choisi
aléatoirement à chaque fois que vous recommencez

1- Mettre le tableau en place (tableau index 0 ou index 1)
            
Brouillard de guerre
Mur (caché par brouillard de guerre)
Souris (caché par brouillard de guerre)
Chat
2- Pouvoir bouger a l'aide des boutons type submit (haut, bas, gauche, droite)
3- Disparaitre le brouillard (voir mur ou espace libre) / cacher les autres cases qui sont plus loin 
4- Si il y a un mur et que l'utilisateur utilise la fleche vers le mur -> Erreur
5- Si il y a la souris et que l'utilisateur utilise la fleche vers la souris -> Victoire  -->

<?php

session_start();

function restartGame()
{

    $labyrinthes = [
        [
            [2, 0, 0, 1, 0, 0],
            [1, 1, 0, 0, 0, 0],
            [1, 0, 0, 1, 1, 3],

        ],
        [
            [2, 1, 0, 1, 1, 0],
            [0, 0, 0, 0, 0, 0],
            [1, 1, 1, 0, 1, 3],
        ]
    ];

    $labyrinthe = $labyrinthes[array_rand($labyrinthes)];
    $_SESSION['labyrinthe'] = $labyrinthe;
    $_SESSION['chat'] = ['x' => 0, 'y' => 0];
    $_SESSION['souris'] = ['x' => 5, 'y' => 2];
    $_SESSION['gameOver'] = false;
}

if (!isset($_SESSION['labyrinthe']) || !isset($_SESSION['gameOver'])) {
    restartGame();
}

$labyrinthe = $_SESSION['labyrinthe'];
$positionChat = $_SESSION['chat'];
$souris = $_SESSION['souris'];
$gameOver = $_SESSION['gameOver'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["restart"])) {
        session_destroy();
        session_start();
        restartGame();
        $labyrinthe = $_SESSION['labyrinthe'];
        $positionChat = $_SESSION['chat'];
        $souris = $_SESSION['souris'];
        $gameOver = $_SESSION['gameOver'];
    }

    if (isset($_POST['move']) && !$gameOver) {
        $newPosition = $positionChat;
        $move = $_POST['move'];
        switch ($move) {
            case 'up':
                $newPosition['y'] -= 1;
                break;
            case 'down':
                $newPosition['y'] += 1;
                break;
            case 'left':
                $newPosition['x'] -= 1;
                break;
            case 'right':
                $newPosition['x'] += 1;
                break;
        }

        // Vérification des limites et obstacles
        if (
            $newPosition['y'] >= 0 && $newPosition['y'] < count($labyrinthe) &&
            $newPosition['x'] >= 0 && $newPosition['x'] < count($labyrinthe[0]) &&
            $labyrinthe[$newPosition['y']][$newPosition['x']] != 1
        ) {
            $positionChat = $newPosition;
            $_SESSION['chat'] = $positionChat;

            if ($positionChat['x'] == $souris['x'] && $positionChat['y'] == $souris['y']) {
                $_SESSION['gameOver'] = true;
                $gameOver = true;
            }
        } else {
            $errorMessage = '<p style= "font-family: Georgia;"> Mouvement impossible ! </p>';
        }
    } 
}

function showLabyrinthe($labyrinthe, $positionChat, $souris, $gameOver)
{
    $gridWidth = count($labyrinthe[0]); // La largeur de la grille
    $gridHeight = count($labyrinthe); // La hauteur de la grille

    echo '<div style="display: grid; grid-template-columns: repeat(' . $gridWidth . ', 150px);">';
    for ($y = 0; $y < $gridHeight; $y++) {
        for ($x = 0; $x < $gridWidth; $x++) {
            $cell = '<div style="width: 150px; height: 150px; display: flex; align-items: center; justify-content: center; border: 1px solid transparent; box-sizing: border-box;">';

            if (
                ($y === $positionChat['y'] && $x === $positionChat['x']) ||
                ($y === $positionChat['y'] + 1 && $x === $positionChat['x']) ||
                ($y === $positionChat['y'] - 1 && $x === $positionChat['x']) ||
                ($y === $positionChat['y'] && $x === $positionChat['x'] + 1) ||
                ($y === $positionChat['y'] && $x === $positionChat['x'] - 1)
            ) {
                if ($positionChat['x'] == $x && $positionChat['y'] == $y) {
                    $cell .= '<img src="./assets/images/izuku.webp" alt="Izuku" style="width: 100%; height: auto;">'; // Chat
                } elseif ($souris['x'] == $x && $souris['y'] == $y) {
                    $cell .= '<img src="./assets/images/allMight.png" alt="AllMight" style="width: 100%; height: auto;">'; // Souris
                } else {
                    $cell .= $labyrinthe[$y][$x] == 1 ? '<img src="./assets/images/caillou.png"alt="Mur" style="width: 100%; height: auto;">' : ' '; // Mur ou espace libre ?
                }
            } else {
                $cell .= '<img src="./assets/images/herbes.png" alt="fog" style="width: 100%; height: auto;">';
            }
            $cell .= '</div>';
            echo $cell;
        }
    }
    echo '</div>';
    if ($gameOver == true) {
        echo "<p> Vous avez trouve All Might ! Rejouer. </p>";
    }
}


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/style.css">
    <title>Labyrinthe</title>
</head>

<body>
    <header>
        <h1>Le labyrinthe</h1>
    </header>
    <div class="container">
    <?php showLabyrinthe($labyrinthe, $positionChat, $souris, $gameOver); ?>
        <form method="post" class="controls">
            <button type="submit" name="move" value="up">Haut</button>
            <button type="submit" name="move" value="left">Gauche</button>
            <button type="submit" name="move" value="down">Bas</button>
            <button type="submit" name="move" value="right">Droite</button>
            <button type="submit" name="restart">Rejouer</button>
        </form>
        <div class="errorMessageContainer">
        <?php
            if (isset($errorMessage)) {
                echo $errorMessage;
            }
            ?>
        </div>
    </div>
</body>

</html>