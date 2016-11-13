function chk(ival)
{
 var tot=0;
for (var i=0;i<ival;i++)
{
var x=eval("document.form1."+"chb"+i+".checked");
if(x)
     {
     tot=1;
     }
 }
 if(tot==0)
 {
 alert("Please Select at least one checkbox to update!");
 return false;
 }
}
function chk1(x)
{
alert(x);
}
document.getElementById("btn").onclick = function() {
 if(document.getElementById("btn").value=="Update")
 {
 return chk(document.getElementById("user_appraisal").rows.length-1);
 }
}
document.getElementById("btn1").onclick = function() {
if(document.getElementById("btn1").value == "Submit\ for\ rating")
{if(document.getElementById("user_rating").rows[1].cells[1].innerHTML=="0")
	{
	alert("You should submit for rating after overall rating in Appraisal Rating link");
	return false;
	}
}
}
document.getElementById("btn_del").onclick = function() {
 if(document.getElementById("btn_del").value=="Delete")
 {
 var j=chk(document.getElementById("user_appraisal").rows.length-1);
 alert(j);
 if(j)
 {
 return confirm('Are You Sure you want to Delete this record?');
	}else{return j;}

 }
}
