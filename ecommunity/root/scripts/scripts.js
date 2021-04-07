//if on a mobile device have nav tag taller to make room for compression
var eco_title = document.getElementById('ecommunity_title');
if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
  console.log("mobile");
  eco_title.style.display = 'none';
}else{
  console.log("not mobile");
}

var hello_user = document.getElementById('hello_user');
var user_links = document.getElementById('user_links');

var delete_div = document.getElementById('delete_div');
var delete_div_content = document.getElementById('delete_div_content');
var delete_link = document.getElementById('delete_link');
var cancel_delete = document.getElementById('cancel_delete');

//opens a hidden div when a certain element is clicked on and hides the div again
//when the element clicked on or any space ouside the div are clicked on
var openDivs = function(clicked, div, display, closes_itself, optional_third, optional_fourth){
  var open = function(){
    div.style.display = display;
    clicked.removeEventListener("click", open);
    clicked.addEventListener("click", close_clicked);
  };
  //if the div is opened, close it by pressing the element clicked on to open it
  var close_clicked = function(){
    div.style.display = "none";
    clicked.removeEventListener("click", close_clicked);
    clicked.addEventListener("click", open);
  };
  //close the div by clicking anywhere except the div and the element clicked on to open it
  var close = function(){
    //get the element bing clicked on
    targetElement = event.target;
    do{
      if((targetElement == div && closes_itself == false)|| targetElement == clicked || targetElement == optional_third || targetElement == optional_fourth){
        //end the program, don't close anything
        return;
      }
      // Go up the DOM
      targetElement = targetElement.parentNode;
    } while(targetElement);
    clicked.addEventListener("click", open);
    div.style.display = "none";
  };
  clicked.addEventListener("click", open);
  document.addEventListener("click", close);
};
openDivs(hello_user, user_links, 'table', true, delete_link, delete_div);
openDivs(delete_link, delete_div, 'block', true, delete_div_content);


function cancelDelete(){
  delete_div.style.display = "none";
};
cancel_delete.addEventListener("click", cancelDelete);
