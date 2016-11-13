function chk()
{
var tot=0;
  var val = document.forms["form4"]["ival"].value;
for (var i=0;i<val;i++)
{
var x=eval("document.form4."+"chb"+i+".checked");
   if(x)
     {
     tot=1;
     }
 }
 if(tot==0)
 {
 alert("Please Select at least one checkbox!");
 return false;
 }
}
function validateOB(field) {
var valid = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
var ok = "yes";
var temp;
for (var i=0; i<field.value.length; i++) {
temp = "" + field.value.substring(i, i+1);
if (valid.indexOf(temp) == "-1") ok = "no";
}
if (ok == "no") {
alert("Invalid entry!  Only characters are accepted!");
field.focus();
field.select();
   }
}
function validate1(field) {
var valid = "0123456789";
var ok = "yes";
var temp;
for (var i=0; i<field.value.length; i++) {
temp = "" + field.value.substring(i, i+1);
if (valid.indexOf(temp) == "-1") ok = "no";
}
if (ok == "no") {
alert("Invalid entry!  Only numbers are accepted!");
field.focus();
field.select();
   }
}
function validate(field)
{
var j;
for(j = 0; j<document.forms["form1"]["jval"].value; j++)
{
var k=document.forms["form1"]["sa_tb"+ j].value;
if (k>24){alert("Invalid entry!"+" "+"cannot be more then 24 hours."); return false;}
else if(isNaN(k)){alert("Enter digits only!"); return false;}

var l=document.forms["form1"]["su_tb"+ j].value;
if (l>24){alert("Invalid entry!"+" "+"cannot be more then 24 hours."); return false;}
else if(isNaN(l)){alert("Enter digits only!"); return false;}

var m=document.forms["form1"]["mo_tb"+ j].value;
if(m>24){alert("Monday's working hours cannot be greater then 24 hours.");return false;}
else if(isNaN(m)){alert("Enter digits only!");return false;}

var n=document.forms["form1"]["tu_tb"+ j].value;
if(n>24){alert("Tuesday's working hours cannot be greater then 24 hours.");return false;}
else if(isNaN(n)){alert("Enter digits only!");return false;}

var o=document.forms["form1"]["we_tb"+ j].value;
if(p>24){alert("Wednesday's working hours cannot be greater then 24 hours.");return false;}
else if(isNaN(p)){alert("Enter digits only!");return false;}

var q=document.forms["form1"]["th_tb"+ j].value;
if(q>24){alert("Thrusday's working hours cannot be greater then 24 hours.");return false;}
else if(isNaN(q)){alert("Enter digits only!");return false;}

var r=document.forms["form1"]["fr_tb"+ j].value;
if(r>24){alert("Friday's working hours cannot be greater then 24 hours.");return false;}
else if(isNaN(r)){alert("Enter digits only!");return false;}
}
}

function validateForm1()
{
var i;
for(i = 0; i<document.forms["form1"]["ival"].value; i++)
 {
 var a=document.forms["form1"]["cha"+ i].value;
  if (a==null || a=="")
  {alert("Please select charge code!");return false;}
 
 var b=document.forms["form1"]["pro"+ i].value;
  if (b==null || b=="")
  {alert("Please select project!");return false;}
  
 var c=document.forms["form1"]["tas"+ i].value;
  if (c==null || c=="")
  {alert("Please Provide task!");return false;}
 
 var d=document.forms["form1"]["sat"+ i].value;
  if (d>24){alert("Invalid entry!"+" "+"cannot be more then 24 hours."); return false;}
  else if(isNaN(d)){alert("Enter digits only!"); return false;}
 
 var e=document.forms["form1"]["sun"+ i].value;
  if (e>24){alert("Invalid entry!"+" "+"cannot be more then 24 hours."); return false;}
  else if(isNaN(e)){alert("Enter digits only!"); return false;}
 
 var f=document.forms["form1"]["mon"+ i].value;
  if(f>24){alert("Monday's working hours cannot be greater then 24 hours.");return false;}
  else if(isNaN(f)){alert("Enter digits only!");return false;}
 
 var g=document.forms["form1"]["tue"+ i].value;
  if(g>24){alert("Tuesday's working hours cannot be greater then 24 hours.");return false;}
  else if(isNaN(g)){alert("Enter digits only!");return false;}
 
 var h=document.forms["form1"]["wed"+ i].value;
  if(h>24){alert("Wednesday's working hours cannot be greater then 24 hours.");return false;}
  else if(isNaN(h)){alert("Enter digits only!");return false;}
 
 var i=document.forms["form1"]["thr"+ i].value;
  if(i>24){alert("Thursday's working hours cannot be greater then 24 hours.");return false;}
  else if(isNaN(i)){alert("Enter digits only!");return false;}
 
 var j=document.forms["form1"]["fri"+ i].value;
  if(j>24){alert("Friday's working hours cannot be greater then 24 hours.");return false;}
  else if(isNaN(j)){alert("Enter digits only!");return false;}
 }
return validate(field);
}

function validateForm2a()
{
var a=document.forms["form2"]["empids"].value;
var b=document.forms["form2"]["empname"].value;
var c=document.forms["form2"]["charge"].value;
var d=document.forms["form2"]["smonth"].value;
var e=document.forms["form2"]["day"].value;
if ((a==null || a=="") && (b==null || b=="") && (c==null || c=="") && (d==null || d=="") && (e==null || e==""))
  {
  alert("Please Provide Search Condition!");
  return false;
  }
}

function validateForm2b()
{
var x=document.forms["form2"]["smonth"].value;
var y=document.forms["form2"]["day"].value;
if ((x==null || x=="") && (y==null || y==""))
  {
  alert("Please Provide Search Condition!");
  return false;
  }
}

function validateForm3()  
{
var x=document.forms["form3"]["cha"].value;
if (x==null || x=="")
  {
  alert("Please Select Charge Code!");
  return false;
  }
  
  var x=document.forms["form3"]["pname"].value;
  if (x==null || x=="")
    {
    alert("Please Enter Project Name!");
    return false;
  }
  
  var x=document.forms["form3"]["pcode"].value;
  if (x==null || x=="")
    {
    alert("Please Enter Project Code!");
    return false;
  }
  
  var x=document.forms["form3"]["pdesc"].value;
  if (x==null || x=="")
    {
    alert("Please Enter Project Description!");
    return false;
  }
  
  var x=document.forms["form3"]["sdate"].value;
  if (x==null || x=="")
    {
    alert("Please Provide Start Date!");
    return false;
  }
  
  var x=document.forms["form3"]["edate"].value;
  if (x==null || x=="")
    {
    alert("Please Provide End Date!");
    return false;
  }
  
  var x=document.forms["form3"]["stat"].value;
  if (x==null || x=="")
    {
    alert("Please Provide Status!");
    return false;
  }
}

function validateForm4()
{
var i; var tot=0;
for(i = 0; i < document.forms["form4"]["ival"].value ; i++)
 {
 var x=document.forms["form4"]["pname"+ i].value;
  if (x==null || x=="")
  {
  alert("Project name is empty!");
  return false;
  }
  
 var y=document.forms["form4"]["pdesc"+ i].value;
  if (y==null || y=="")
  {
  alert("Project description is empty!");
  return false;
  }  
 }
}

function validateForm5()  
{
var x=document.forms["form5"]["eid"].value;
if (x==null || x=="")
  {
  alert("Please Provide Employee ID!");
  return false;
  }
  
var x=document.forms["form5"]["uname"].value;
if (x==null || x=="")
  {
  alert("Please Provide Employee Name!");
  return false;
  }
  
var x=document.forms["form5"]["upassword"].value;
if (x==null || x=="")
  {
  alert("Please Provide Employee Password!");
  return false;
  }
  
var x=document.forms["form5"]["dojoin"].value;
if (x==null || x=="")
  {
  alert("Please Provide Employee Date Of Joining!");
  return false;
  }  
}

function validateForm6()
{
var i;
for(i = 0; i < document.forms["form6"]["ival"].value ; i++)
 {
 var x=document.forms["form6"]["uname"+ i].value;
  if (x==null || x=="")
  {
  alert("Username is empty");
  return false;
  }
  
 var y=document.forms["form6"]["upass"+ i].value;
  if (y==null || y=="")
  {
  alert("password is empty");
  return false;
  }
 }
}

function validateForm7()  
{
var x=document.forms["form7"]["tname"].value;
if (x==null || x=="")
  {
  alert("Please Enter New Task!");
  return false;
  }
}

function validateForm8()  
{
var x=document.forms["form8"]["dtask"].value;
if (x==null || x=="")
  {
  alert("Please Select Task To Delete!");
  return false;
  }
}

function validateForm9()  
{
var x=document.forms["form9"]["ccname"].value;
if (x==null || x=="")
  {
  alert("Please Enter Charge Code!");
  return false;
  }
}

function validateForm10()  
{
var x=document.forms["form10"]["dcode"].value;
if (x==null || x=="")
  {
  alert("Select Charge Code To Delete!");
  return false;
  }
}






function validateForm(form,field)
{
var x=document.forms[form][field].value;
if (x==null || x=="")
  {
  alert(field+"cannot be blank");
  return false;
  }
}