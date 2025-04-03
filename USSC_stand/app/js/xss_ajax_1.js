
var xmlHttp = createXmlHttpRequestObject(); 


function createXmlHttpRequestObject() 
{	

    var xmlHttp;

    if(window.ActiveXObject)
    {
        try
        {
            xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        catch (e)
        {
            xmlHttp = false;
        }
    }

    else
    {
        try
        {
            xmlHttp = new XMLHttpRequest();
        }
        catch (e)
        {
            xmlHttp = false;
        }
    }

    if(!xmlHttp)
        alert("Error creating the XMLHttpRequest object.");
    else 
        return xmlHttp;
}


function process()
{

    if(xmlHttp.readyState == 4 || xmlHttp.readyState == 0)
    {

        title = encodeURIComponent(document.getElementById("title").value);

        xmlHttp.open("GET", "xss_ajax_1-2.php?title=" + title, true);  

        xmlHttp.onreadystatechange = handleServerResponse;

        xmlHttp.send(null);
    }
    else

        setTimeout("process()", 1000);
}


function handleServerResponse() 
{

    if(xmlHttp.readyState == 4) 
    {

        if(xmlHttp.status == 200) 
        {

            xmlResponse = xmlHttp.responseXML;

            xmlDocumentElement = xmlResponse.documentElement;

            result = xmlDocumentElement.firstChild.data;

            document.getElementById("result").innerHTML = result;

            setTimeout("process()", 1000);
        } 

        else 
        {
            alert("There was a problem accessing the server: " + xmlHttp.statusText);
        }
    }
}