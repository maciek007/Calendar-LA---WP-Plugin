
var added = 1;

inputs = document.getElementById('la-calendar-admin-table').getElementsByTagName('input');

for(inp of inputs)
{
    if(inp.type!='button' && inp.type!='hidden')
    {
        inp.addEventListener("change", setEdited);
    }
}
inputs = document.getElementById('la-calendar-admin-table').getElementsByTagName('textarea');
for(inp of inputs)
{
    inp.addEventListener("change", setEdited);
}

function setEdited(event)
{
    inp = event.target;
    prefix = inp.name.slice(0,inp.name.indexOf('_'));
    document.getElementsByName(prefix+"_edited")[0].value=1;

}

function insertColumn(row, content)
{
    cell = row.insertCell()
    cell.innerHTML=content
}

function addNewGame()
{
    id = '*'+added;
    row = document.getElementsByClassName('la-calendar-admin-table')[0].children[1].insertRow(0);

    insertColumn(row, id);

    ////////////////////////////////////////// Name ////////////////////////////////////////////////////////
    
    insertColumn(row, '<br><textarea required name="game'+id+'_name"></textarea>');

        
    ///////////////////////////////////////// Date //////////////////////////////////////////////////////////

    insertColumn(row, '<div>Data rozpoczęcia: <input required type="date" name="game'+id+'_date_start"/></div></br>     \
                       <div>Data zakończenia (opcjonalne): <input type="date" name="game'+id+'_date_end"/></div>');

    ///////////////////////////////////////// City ///////////////////////////////////////////////////////////
    insertColumn(row, '<br/><input type="text" name="game'+id+'_city"/>');


    ///////////////////////////////////////// Post link ////////////////////////////////////////////////////
    insertColumn(row, 'link:<br/><input type="text" name="game'+id+'_post"/>');

    ///////////////////////////////////////// Files //////////////////////////////////////////////////////////
    // Name [link]

    insertColumn(row, '<div class="la-files-plus"><input type="button" onclick="addNewFile(this)" value="+" class="button button-primary button-small"/></div>');

    

    ///////////////////////////////////////// Links ///////////////////////////////////////////////////////////
    //starter
    //live_results	
    //livestream
    tmp = "";
    //$tmp = '<div class="links_flex">';
        tmp += 'kalendarz pzla: <br/><input name="game'+id+'_kalendarz_pzla" type="text"/><br/>';
        tmp += 'starter: <br/><input type="text" name="game'+id+'_starter"/><br/>';
        tmp += 'wyniki na żywo: <br/><input type="text" name="game'+id+'_live_results"/><br/>';
        tmp += 'transmisja: <br/><input type="text" name="game'+id+'_livestream"/>';
        tmp += 'organizator: <br/><input type="text" name="game'+id+'_organizator"/>';
    //$tmp .= "</div>";
    insertColumn(row, tmp);

    ////////////////////////////////////////// Results /////////////////////////////////////////////////////////

    insertColumn(row, "link:<br/><input type=\"text\" name=\"game"+id+"_results\"/>");
    row.innerHTML+= '<td style="align-content: center"><a class="submitdelete" style="color:#b32d2e" href="#" onclick="deleteRecord(this)">Usuń</a>';
    added+=1;
}

function addNewFile(button) {
    div = document.createElement("div");
    div.className = "la-files-flex";
    id = button.parentElement.parentElement.parentElement.children[0].innerHTML;
    i = button.parentElement.parentElement.children.length - 1;
    div.innerHTML = "\
    <div>\
        <div>Nazwa</div>\
        <input name=\"game"+id+"_files_name_"+i+"\" style=\"width:10em\" type=\"text\"/>\
    </div>\
    <div style=\"flex:1\">\
        <div>Link</div>\
        <input name=\"game"+id+"_files_link_"+i+"\" type=\"text\"/>\
    </div>";

    if(id[0]!='*')
        document.getElementsByName("game"+id+"_edited")[0].value=1;

    button.parentElement.before(div);
}

function deleteRecord(btn) {
    btn.parentElement.parentElement.remove();
}

jQuery(document).ready(function($) { 
 
    $(document).ready(function() {
        needToConfirm = false;
        window.onbeforeunload = askConfirm;
    });
     
    function askConfirm() {
        if (needToConfirm) {
            // Put your custom message here
            return "Your unsaved data will be lost.";
        }
    }
     
    $("#la_form").change(function() {
        needToConfirm = true;
    });
    $("#la_form").submit(function() {
        needToConfirm = false;
    });
})

