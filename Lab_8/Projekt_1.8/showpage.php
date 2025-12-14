<?php
include('cfg.php');

function PokazPodstrone($id)
{
    global $link;
    $id_clear = mysqli_real_escape_string($link, $id);
    $query = "SELECT page_content FROM page_list WHERE id='$id_clear' AND status=1 LIMIT 1";
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_array($result);
    
    $web = '';

    if(empty($row['page_content']))
    {
        $web = '[nie_znaleziono_strony]';
        echo $web;
    }
    else
    {
        $tresc_do_wykonania = $row['page_content'];
        
        eval('?>'. $tresc_do_wykonania . '<?php');
    }
    
    return;
}


function PokazMenu()
{
    global $link;
    $query = "SELECT id, page_title FROM page_list WHERE status=1 ORDER BY id ASC";
    $result = mysqli_query($link, $query);

    $menu = '';
    while($row = mysqli_fetch_assoc($result)) {
        $menu .= '<li><a href="index.php?id='.$row['id'].'">'.htmlspecialchars($row['page_title']).'</a></li>';
    }
    return $menu;
}

?>
