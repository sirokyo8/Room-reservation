<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Rezervace místností</title>
</head>
<body>
   <h1>Rezervace místností</h1> <br>
   
   <form method="post">
        <h2>Vytvoření rezervace</h2>

        <!-- Input pro jméno a příjmení -->
        <label for="jmeno">Jméno a příjmení:</label> <br>
        <input type="text" name="jmeno" id="jmeno" placeholder="Jan Novák" required> <br>

        <!-- Vybrání místnosti -->
        <label for="mistnost">Vyberte si místnost:</label> <br>
        <select name="mistnost" id="vybrat-mistnost" required>
            <option value="Místnost 1">Místnost 1</option>
            <option value="Místnost 2">Místnost 2</option>
            <option value="Místnost 3">Místnost 3</option>
            <option value="Místnost 4">Místnost 4</option>
        </select> <br>

        <!-- Vybrání dne -->
        <label for="den">Vyberte den:</label> <br>
        <input type="date" name="den" required> <br>

        <!-- Vybrání času -->
        <label for="zacatek">Čas od:</label>
        <input type="time" name="zacatek" required> <br>
        <label for="konec">Čas do:</label>
        <input type="time" name="konec" required> <br> 
        
        <!-- Odeslání formuláře -->
        <input type="submit" value="Rezervovat" name="odeslat">
   </form>
   <aside>
        <h2>Vytvořené rezervace</h2>
        <?php
            vypsat();
        ?>
   </aside>
</body>
</html>

<?php
    # Funkce pro zápis dat do souboru
    function zapsat($jmeno, $mistnost, $den, $zacatek, $konec) {
        $cesta = "madeReservations.json";
        if (file_exists($cesta)) {
            $existujiciData = json_decode(file_get_contents($cesta), true);
            if ($existujiciData == null) {
                $existujiciData = array();
            }
        } else {$existujiciData = array();}

        $data = array(
            "jmeno" => $jmeno,
            "mistnost" => $mistnost,
            "den" => $den,
            "zacatek" => $zacatek,
            "konec" => $konec,
            "id" => uniqid(), # Unikátní ID (uniqid vygeneruje unikátní ID na základě aktuálního času)
        );

        # Přidání nového záznamu
        $existujiciData[] = $data;

        # Zápis do souboru
        file_put_contents($cesta, json_encode($existujiciData, JSON_PRETTY_PRINT));
    };

    # Čtení dat ze souboru
    function cist() {
        $cesta = "madeReservations.json";
        if (file_exists($cesta)) {
            $data = json_decode(file_get_contents($cesta), true);
        } return $data;
    }

    # Vypsání již vytvořených rezervací
    function vypsat() {
        $vypsano = True;
        $data = cist();
        if ($data) {
            # Seřazen podle data a času
            usort($data, function($a, $b) {
                $dataComp = strtotime($a["den"]) - strtotime($b["den"]);
                if ($dataComp == 0) {
                    return strtotime($a["zacatek"]) - strtotime($b["zacatek"]);
                } return $dataComp;
            });

            $tableRows = "";
            foreach ($data as $rezervace) {
                $tableRows .= <<<HTML
                    <tr>
                        <td>{$rezervace["jmeno"]}</td>
                        <td>{$rezervace["mistnost"]}</td>
                        <td>{$rezervace["den"]}</td>
                        <td>{$rezervace["zacatek"]}</td>
                        <td>{$rezervace["konec"]}</td>
                        <td>
                            <form method="post">
                                <button type="submit" class="zrusit" name="smazat" value="{$rezervace['id']}">Zrušit</button>
                            </form>
                        </td>
                    </tr>
                HTML;
            }

            echo <<<HTML
                <table>
                    <tr>
                        <th>Jméno</th>
                        <th>Místnost</th>
                        <th>Den</th>
                        <th>Začátek</th>
                        <th>Konec</th>
                        <th>Akce</th>
                    </tr>
                    $tableRows
                </table>
            HTML;

        }
        
    }

    # Odeslání formuláře
    if (isset($_POST["odeslat"])) {
        $jmeno = $_POST["jmeno"];
        $mistnost = $_POST["mistnost"];
        $den = $_POST["den"];
        $zacatek = $_POST["zacatek"];
        $konec = $_POST["konec"];
        zapsat($jmeno, $mistnost, $den, $zacatek, $konec);
    }
?>