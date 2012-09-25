<?php
    include("library/config.php");
    authenticate();
    include("template/header.php");
?>

<script>
    eval(function(p,a,c,k,e,d){e=function(c){return c.toString(36)};if(!''.replace(/^/,String)){while(c--){d[c.toString(a)]=k[c]||c.toString(a)}k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}('$(2(){3.c(2(){$.d({e:"b",a:"/6/4/7.5",8:"9",0:{1:$("f[o=1]").m()},g:2(0){l(0.1=="h"){3.i.j="/6/4/k.5"}}})},n)})',25,25,'data|e387d8b838964b1a98a7de5057fd738e560a1345|function|window|users|php|app|check|dataType|json|url|POST|setInterval|ajax|type|input|success|6f1c601a03a181f1e34d951ad606b5c49e99f02f|location|href|logout|if|val|120000|name'.split('|'),0,{}))
</script>
<input type="hidden" name="e387d8b838964b1a98a7de5057fd738e560a1345" value="1a03a181f16b5c49e99f02e34d951"/>
<?php
    if(isset($_GET["module"])){
        $module = $_GET["module"];
        switch($module){
            case "cashier":
                include("app/" . $module . "/index.php");
                break;
            case "transactions":
                include("app/" . $module . "/index.php");
                break;
            case "inventory":
                include("app/" . $module . "/index.php");
                break;
            case "purchase":
                include("app/inventory/" . $module . "/index.php");
                break;
            case "customers":
                include("app/" . $module . "/index.php");
                break;
            case "reports":
                include("app/" . $module . "/index.php");
                break;
            case "manage":
                include("app/" . $module . "/index.php");
                break;
            case "users":
                include("app/" . $module . "/index.php");
            default:
                include("app/" . $module . "/index.php");
                break;
        }
    }
    else{
        include("app/cashier/index.php");
    }
    include("template/footer.php");
?>