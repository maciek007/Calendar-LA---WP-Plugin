<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

add_action( 'admin_menu', 'calendar_LA_manage_games' );


function calendar_LA_manage_games() {
    $hookname = add_menu_page(
        'Zarządzaj zawodami',
        'Zawody',
        'edit_posts',
        'la_manage',
        'calendar_LA_manage_games_html',
        'dashicons-calendar-alt',
        6
    );
    add_action( 'load-' . $hookname, 'calendar_LA_manage_games_submit' );
}

function calendar_LA_manage_games_submit()
{
    global $wpdb;

    if('POST' === $_SERVER['REQUEST_METHOD'])
    {

        $table = $wpdb->get_results("
        SELECT id
        FROM {$wpdb->prefix}la_competitions
        ORDER BY `date_start` DESC
        ", OBJECT);
        
        foreach ( $table as $record ) {
            $prefix = 'game'.$record->id.'_';
            if(!isset($_POST[$prefix.'name']))
            {
                $wpdb->delete( "{$wpdb->prefix}la_competitions", array( 'ID' => $record->id), array( '%d' ) );
            }
            elseif($_POST[$prefix.'edited'] == 1)
            {
                $name = $_POST[$prefix.'name'];
                $date_start = $_POST[$prefix.'date_start'];
                $date_end = $_POST[$prefix.'date_end'];
                $city = $_POST[$prefix.'city'];
                $post_link = $_POST[$prefix.'post'];
                
                //files
                $files = '';
                $no_file=0;
                while(isset($_POST[$prefix.'files_name_'.$no_file]))
                {
                    $f_name = $_POST[$prefix.'files_name_'.$no_file];
                    $f_link = $_POST[$prefix.'files_link_'.$no_file];
                    if($f_name!='' && $f_link!='')
                        $files .= $f_name."[{$f_link}]";
                    $no_file+=1;
                }

                ///

                $kalendarz = $_POST[$prefix.'kalendarz_pzla'];
                $livestream = $_POST[$prefix.'livestream'];
                $domtel = $_POST[$prefix.'live_results'];
                $starter = $_POST[$prefix.'starter'];
                $results = $_POST[$prefix.'results'];
                $organizator = $_POST[$prefix.'organizator'];


                $insert_array = array(
                    'name' => $name,
                    'city' => $city,
                    'date_start' => $date_start,
                    'date_end' => $date_end,
                    'rel_post' => $post_link,
                    'results' => $results,
                    'files' => $files,
                    'pzla' => $kalendarz,
                    'live_results' => $domtel,
                    'livestream' => $livestream,
                    'starter' => $starter,
                    'organizator' => $organizator
                );
                $insert_array = array_filter($insert_array);

                $result = $wpdb->update("{$wpdb->prefix}la_competitions", $insert_array, array( 'ID' => $record->id),  null, array( '%d' ) );
            }
        }

        $id = 1;
        $skipped = 0;
        
        while(isset($_POST['game*'.$id.'_name']) || $skipped<5)
        {
            if(isset($_POST['game*'.$id.'_name']))
            {
                $skipped=0;
            }
            else
            {
                $skipped+=1;
                $id+=1;
                continue;
            }
            $prefix = 'game*'.$id.'_';
            $name = $_POST[$prefix.'name'];
            $date_start = $_POST[$prefix.'date_start'];
            $date_end = $_POST[$prefix.'date_end'];
            $city = $_POST[$prefix.'city'];
            $post_link = $_POST[$prefix.'post'];
            
            //files
            $files = '';
            $no_file=0;
            while(isset($_POST[$prefix.'files_name_'.$no_file]))
            {
                $f_name = $_POST[$prefix.'files_name_'.$no_file];
                $f_link = $_POST[$prefix.'files_link_'.$no_file];
                if($f_name!='' && $f_link!='')
                    $files .= $f_name."[{$f_link}]";
                $no_file+=1;
            }

            ///

            $kalendarz = $_POST[$prefix.'kalendarz_pzla'];
            $livestream = $_POST[$prefix.'livestream'];
            $domtel = $_POST[$prefix.'live_results'];
            $starter = $_POST[$prefix.'starter'];
            $results = $_POST[$prefix.'results'];
            $organizator = $_POST[$prefix.'organizator'];

            $insert_array = array(
                'name' => $name,
                'city' => $city,
                'date_start' => $date_start,
                'date_end' => $date_end,
                'rel_post' => $post_link,
                'results' => $results,
                'files' => $files,
                'pzla' => $kalendarz,
                'live_results' => $domtel,
                'livestream' => $livestream,
                'starter' => $starter,
                'organizator' => $organizator
            );
            $insert_array = array_filter($insert_array);

            $result = $wpdb->insert("{$wpdb->prefix}la_competitions", $insert_array);
            $id+=1;
        }
    }
}

function calendar_LA_manage_games_html(){
	wp_enqueue_style('LA_calendar_admin',plugins_url('admin/css/la_calendar_admin.css', 'calendar-LA/calendar-LA.php' ));
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="<?php menu_page_url( 'la_manage' ) ?>" method="post" class="postbox" id="la_form">
            <div id="major-publishing-actions"><input type="submit" class="button button-primary button-large" value="Aktualizuj"/></div>
        	<table class="wp-list-table widefat striped table-view-list la-calendar-admin-table" id="la-calendar-admin-table">
                <thead>
                    <tr>
                        <th style="width:1em">ID</th><th>Nazwa</th> <th style="width:8em">Data</th><th>Miejsce</th><th> Post </th><th>Pliki</th><th>Odnośniki</th><th>Wyniki</th><th style="width:3em"></th>
                    </tr>
                    <tr>
                        <th colspan="9" style="text-align:center"><input type="button" onclick="addNewGame()" value="Dodaj" class="button button-primary button-small"/></th>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php
            global $wpdb;
            $table = $wpdb->get_results("
            SELECT *, year(`date_start`) AS year
            FROM {$wpdb->prefix}la_competitions
            ORDER BY `date_start` DESC
            ", OBJECT);
            
            foreach ( $table as $record ) {
                $year = $record->year;
                ?>
                <tr>
                    <?php
        
        $values = array();
        
        $values[] = $record->id;
        
        ////////////////////////////////////////// Name ////////////////////////////////////////////////////////
        
        $values[] = '<br><textarea required name="game'.$record->id.'_name">'.stripslashes($record->name).'</textarea>';
        
        
        ///////////////////////////////////////// Date //////////////////////////////////////////////////////////
        
        $values[] = '
                            <div>Data rozpoczęcia: <input required type="date" name="game'.$record->id.'_date_start" value="'.$record->date_start.'"/></div></br>
                            <div>Data zakończenia (opcjonalne): <input type="date" name="game'.$record->id.'_date_end" value="'.$record->date_end.'"/></div>
                            ';
                            
                            ///////////////////////////////////////// City ///////////////////////////////////////////////////////////
                            $values[] = '<br/><input type="text" name="game'.$record->id.'_city" value="'.$record->city.'"/>';
                            
                            
                            ///////////////////////////////////////// Post link ////////////////////////////////////////////////////
                            $values[]= 'link:<br/><input type="text" name="game'.$record->id.'_post" value="'. $record->rel_post . '"/>';
                            
                            ///////////////////////////////////////// Files //////////////////////////////////////////////////////////
                            // Name [link]
                            $pattern = "/([^\[]+)\[([^\[\]]+)\]/i";
                            $tmp = "";
                            if($record->files != NULL)
                            if(preg_match_all($pattern, $record->files, $matches)) {
                                for($i = 0; $i < count($matches[0]);$i+=1)
                        {
                            $tmp .= "<div class=\"la-files-flex\">
                            <div>
                            <div>Nazwa</div>
                            <input name=\"game{$record->id}_files_name_{$i}\" style=\"width:10em\" type=\"text\" value=\"{$matches[1][$i]}\"/>
                            </div>
                            <div style=\"flex:1\">
                            <div>Link</div>
                            <input name=\"game{$record->id}_files_link_{$i}\" type=\"text\" value=\"{$matches[2][$i]}\"/>
                            </div>
                            </div>";
                        }
                    }
                    $tmp .= '<div class="la-files-plus"><input type="button" onclick="addNewFile(this)" value="+" class="button button-primary button-small"/></div>';
                    $values[] = $tmp;
                    
                    
                    
                    ///////////////////////////////////////// Links ///////////////////////////////////////////////////////////
                    //starter
                    //live_results	
                    //livestream
                    $tmp = "";
                    //$tmp = '<div class="links_flex">';
                    $tmp .= 'kalendarz pzla: <br/><input name="game'.$record->id.'_kalendarz_pzla" type="text" value="'.$record->pzla.'"/><br/>';
                    $tmp .= 'starter: <br/><input type="text" name="game'.$record->id.'_starter" value="'.$record->starter.'"/><br/>';
                    $tmp .= 'wyniki na żywo: <br/><input type="text" name="game'.$record->id.'_live_results" value="'.$record->live_results.'"/><br/>';
                    $tmp .= 'transmisja: <br/><input type="text" name="game'.$record->id.'_livestream" value="'.$record->livestream.'"/><br/>';
                    $tmp .= 'organizator: <br/><input type="text" name="game'.$record->id.'_organizator" value="'.$record->organizator.'"/>';

                    //$tmp .= "</div>";
                    $values[]= $tmp;
                    
                    ////////////////////////////////////////// Results /////////////////////////////////////////////////////////
                    
                    $values[] = "link:<br/><input type=\"text\" name=\"game{$record->id}_results\" value=\"{$record->results}\"/>";
                    
                    
                    
                    foreach($values as $v)
                    {
                        echo "<td>{$v}</td>";
                    } 
                    ?>
                <td style="align-content: center"><a class="submitdelete" style="color:#b32d2e" href="#" onclick="deleteRecord(this)">Usuń</a>
                <input type="hidden" name="<?php echo "game{$record->id}_edited" ?>" value="0"/>
                
            </tr>
            <?php
            }
            
            // end table
            
            ?>
            </tbody>
        </table>
        
    </form>
    </div>
    <?php
    wp_enqueue_script('la_calendar_admin_js',plugins_url('admin/js/la_calendar_admin_js.js', 'calendar-LA/calendar-LA.php' ));
}

?>