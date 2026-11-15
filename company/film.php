<?php
include './includer.php';
$pagetitle = 'تیزر صفحه اصلی';
$Tabpage = '6';
$thisaccess = '1';
include './header.inc.php';
echo '<section class="content-header">
      <h1>'.$pagetitle.'</h1>
    </section>
        <section class="content"><div class="col-md-12">
<div class="box box-body">';
        // list content

$result = Query("Select * From tbl_film");
$read = mysqli_fetch_array($result);
$exp = explode(",", $read['tbl_film_film']);
global $result,$read,$exp;
$img_item = 1;
$directory = '../upload/';

echo (!empty($error) ? '<div class="error"><span>'.$error.'</span></div>' : '' );
if(!isset($do))
{

    echo '<form action="?do=update" method="POST" enctype="multipart/form-data"><table>';
        for($i=1; $i<=$img_item; $i++)
            {
                if(empty($exp[$i]))
                    {
                    // if not image shod no=ull content 
                        $img_icon = '';
                    }
                else
                    {
                        $img_icon = '<td><a href="'.$directory.$exp[$i].'" target="_blank" ><span class="btn btn-xs btn-success"><span class="bx bxs-show"></span></span></a><a href="?do=delete&img='.$exp[$i].'" ><span class="btn btn-xs btn-danger"><span class="bx bxs-trash"></span></span></a></td>';
                    }
                echo '<div class="form-group"><table ><tr>
                      <td><label class="col-sm-12 control-label">فیلم</label></td>
                      <td><input type="file" name="img_'.$i.'"></td>'.$img_icon.'
                      </tr><table></div>';
            }
                echo '<hr><div class="form-group"><input type="submit" name="submit" value="'.$lang[20].'" class="btn btn-primary"></div></form>';

}

    if(isset($do) && $do == 'update')
    {
       if(isset($_POST['submit']))
{

    $name='';
    for($i=0; $i<=$img_item; $i++)
    {
        
        $file[$i]=$_FILES['img_'.$i]['name'];
        $ext = explode(".", $file[$i]);
        $extn = end($ext);
        $file[$i] = time().'_'.$i.'.'.$extn;
        if($extn == 'mp4' || $extn == 'MP4')
        {
            $name .= $file[$i].',';
            move_uploaded_file($_FILES['img_'.$i]["tmp_name"],"$directory" . $file[$i]);

        }
        elseif (!empty($exp[$i])) {
             $name .=$exp[$i].',';
         }

        else {
                $name .= ',';
            }
      
    }
    $result = Query("SELECT * FROM tbl_film");
    $row_slide = mysqli_num_rows($result);
    if($row_slide == '1')
    {
       $update = Query("Update tbl_film SET tbl_film_film='$name' "); 
    }
        else 
            {
                $insert = Query("Insert INTO tbl_film (tbl_film_film) values('$name') ");
            }
    
        $_SESSION['error'] = $lang[21];
        header("Location: film.php");
        exit();
}
    }
    
    if(isset($do) && $do == 'delete')
        {
           echo $lang[25].'<br /><img src="'.$directory.$img.'" style="width:15%; height:15%; margin:15px;"><br /> <form action="?do=deleted" method="POST"><input type="hidden" value="'.$img.'" name="img_name" >
              <input type="submit" name="submit" value="'.$lang[26].'" >
              <input type="submit" name="nodelete" value="'.$lang[27].'" ></form>';
    
        }
    if (isset($do) && $do == 'deleted')
        {
            if(isset($_POST['submit']))
         {
        $img_name = $_POST['img_name'];
        $record = str_replace("$img_name","",$read['tbl_film_film']);
        $update = Query("Update tbl_film SET tbl_film_film = '$record'");
            if($update)
        {
             $external_file = $directory.$img_name;
             unlink("$external_file");
             $_SESSION['error'] = $lang[28];
             header("Location: film.php");
        }
      
    }
      if(isset($_POST['nodelete']))
    {
        header("Location: film.php");
    }
}
    echo '</div>';
echo '</div></div></section>';
include './footer.inc.php';