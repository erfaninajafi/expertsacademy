<?php

// include
            include './includer.php';
            $pagetitle = $lang[89];
            $Tabpage = '10';
            include './header.inc.php';
// end include 

              echo '<div class="row">
                    <div class="col-lg-12">
                    <h1 class="page-header"> '.$pagetitle.' </h1>
                    </div>
                    <div class="col-lg-11 col-lg-offset-1">';
// list content

              if(isset($do) && $do == 'contact_filter')
              {
                
                echo '<div  class="form-group">'.$lang[97].'</div><br />';
                echo '<form action="csv.php?do=contact_export" method="POST" >';
                
                   
                    //echo ' <div  class="form-group"><input type="checkbox" name="fld1" value="tbl_contact_message_subject"><label class="margin-right">'.$lang[90].'</label> </div>';
                    echo ' <div  class="form-group"><input type="checkbox" name="fld2" value="tbl_contact_message_subject"><label class="margin-right">'.$lang[91].'</label> </div>';
                    echo ' <div  class="form-group"><input type="checkbox" name="fld3" value="tbl_contact_message_name"><label class="margin-right">'.$lang[92].'</label> </div>';
                    //echo ' <div  class="form-group"><input type="checkbox" name="fld4" value="tbl_contact_message_tel"><label class="margin-right">'.$lang[93].'</label> </div>';
                    echo ' <div  class="form-group"><input type="checkbox" name="fld5" value="tbl_contact_message_email"><label class="margin-right">'.$lang[95].'</label> </div>';
                    echo ' <div  class="form-group"><input type="checkbox" name="fld6" value="tbl_contact_message_text"><label class="margin-right">'.$lang[94].'</label> </div>';
                    echo ' <div  class="form-group"><select name="language_select" class="form-control" style="width:150px;"><option value="0">'.$lang[100].'</option>';
                            for($i=1; $i<=$language_count; $i++)
                            {
                                echo '<option value="'.$language[$i].'">'.$language[$i].'</option>';
                            }
                    echo '</select></div>';
               
                echo '<div  class="form-group"> <input type="submit" name="update" value="'.$lang[98].'" class="btn btn-primary"></div></form>';
              }
              if(isset($do) && $do == 'newsletter_filter')
              {
                
                echo '<div  class="form-group">'.$lang[97].'</div><br />';
                echo '<form action="csv.php?do=newsletter_export" method="POST" >';
                
                    echo ' <div  class="form-group"><input type="checkbox" name="fld2" value="tbl_newsletter_name"><label class="margin-right">'.$lang[173].'</label> </div>';
                    echo ' <div  class="form-group"><input type="checkbox" name="fld1" value="tbl_newsletter_email"><label class="margin-right">'.$lang[176].'</label> </div>';
                    echo ' <div  class="form-group"><input type="checkbox" name="fld3" value="tbl_newsletter_mobile"><label class="margin-right">'.$lang[175].'</label> </div>';

               
                echo '<div  class="form-group"> <input type="submit" name="update" value="'.$lang[98].'" class="btn btn-primary"></div></form>';
              }

                //$result = Query('DESCRIBE tbl_contact_message');
//                echo $lang[97].'<br />';
//                echo '<form action="" method="POST" >';
//                while($row = mysqli_fetch_array($result)) {
//                    echo "{$row['Field']}\n";
//                }  