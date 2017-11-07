function show_hide_hidden_fields(db,tb,tb_id,num,sign_id,hidden_no){ // called when you click each table's expansion sign, ie, '+' or '-'
	if(document.getElementById(sign_id).innerHTML=='+') {
		document.getElementById(sign_id).innerHTML='-';
		document.getElementById('table_expanded['+hidden_no+']').value='-';
	}
	else {
		document.getElementById(sign_id).innerHTML='+';
		document.getElementById('table_expanded['+hidden_no+']').value='+';
	}
	var num_hidden=0;
	for(var i=0;i<num;i++){
		var id_name='hd'+db+'.'+tb+'['+i+']';
		if(document.getElementById(id_name)==null) continue;
		if(document.getElementById(id_name).style.display!='none'){ // it was "block"
			document.getElementById(id_name).style.display='none';	
			num_hidden++;
		}
		else {
			document.getElementById(id_name).style.display='inline-block';	 // it was "block"
		}
	}
	document.getElementById(tb_id).style.height=(num-num_hidden)*21+19;
	draw_all_arrows();
}

function fkey(db,ft,ff,tt,y1,y2){ // foreign key object
	this.db_name=db;  //db name for color of the arrows
	this.from_table_id=ft;  //from_table ID
	this.from_table_field=ff;  //from_table ID
	this.to_table_id=tt; // to_table ID
	this.from_y_shrinked=y1; // position from the top of the table when the table is shrunken
	this.from_y_expanded=y2; // position from the top of the table when the table is expanded
}

function DT_to_TbID(dt,id){ // this is only supplementary function to make fkey_info[] complete
	this.name=dt;
	this.id=id;
}

function Table_node(t,d,l,i,tp,n){ //Table node object
	this.tb_no=t; // table no in the DB
	this.db_no=d; // DB no
	this.level=l; // level of the node, 0 means the root
	this.tb_ID=i; // the DIV ID of the table
	this.type=tp; // table type
	this.name=n; // table type
}

function show_field_attrib2(fid,fname){
	$(function() {
		var pos="left+"+(window.click_x+100) +" top+"+(window.click_y-50); // pop-up should appear the right-hand side of mouse click
		$("#field_attrib" ).dialog({position:{my:"left top", at:pos, of:window}, title:fid+fname,minWidth:340});
	  });
	  document.getElementById('field_attrib').innerHTML=" ";	  
	  document.getElementById('field_attrib').innerHTML+="<iframe id='f_iframe' frameborder=0 height=500 width=300></iframe>";
	  document.getElementById('f_iframe').src='field_attrib.php?fid='+fid+'&fname='+fname; // iframe source
}

function show_field_attrib(fid,fname){
	if(is_mult_lang_open) $( "#mult_lang").dialog("close");
	
	var tmp="show_field_attrib2('"+fid+"','"+fname+"')";
	window.setTimeout(tmp,100); // for timing issue: mouse click needs some processing time.
}

function show_mult_lang2(fid,fname,inst){
	  $(function() {
		var pos="left+"+(window.click_x+100) +" top+"+(window.click_y-50); // pop-up should appear the right-hand side of mouse click
		$("#mult_lang" ).dialog({position:{my:"left top", at:pos, of: this}, title:"Field Name:"+fid,minWidth:400});
	  });
	  document.getElementById('mult_lang').innerHTML=" ";	  
	  document.getElementById('mult_lang').innerHTML+="<iframe id='m_iframe' frameborder=0 height=200 width=100%></iframe>";
	  document.getElementById('m_iframe').src='mult_lang.php?fid='+fid+'&fname='+fname+'&inst_no='+inst; // iframe source
	  make_this_zindex_max(fid);
}

function show_mult_lang(fid,fname,inst){
	window.is_mult_lang_open=true;
	var tmp="show_mult_lang2('"+fid+"','"+fname+"','"+inst+"')";	
	window.setTimeout(tmp,100); // for timing issue: mouse click needs some processing time.
}

function assign_table_id(){	
	for(var i=0;i<fkey_info.length;i++){ // assign each fkey_info[i].to_table_id correctly
		for(var j=0;j<myDT_to_TbID.length;j++){
			if(myDT_to_TbID[j].name==fkey_info[i].to_table_id) {
				fkey_info[i].to_table_id=myDT_to_TbID[j].id;
				break; 
			}
			
		}
	}	
}

function toggle_dblclikced_tb(tid){
	if(dbclicked_tb==tid) dbclicked_tb=null;
	else dbclicked_tb=tid;
}
	
function make_this_zindex_max(tid){ // when user click on a table, this table should be top of every table
	var max_idx=get_max_table_zindex();
	var id_no=tid.substring(3,tid.length-1);
	document.getElementById(tid).style.zIndex=parseInt(max_idx)+1;
	document.getElementById('table_zindex['+id_no+']').value=parseInt(max_idx)+1;
}

function get_max_table_zindex(){  // called by make_this_zindex_max()
	var max_zidx=0;
	for(var i=0;i<num_table_printed_so_far;i++){
		if(max_zidx<parseInt(document.getElementById('table_zindex['+i+']').value)) 
			max_zidx=document.getElementById('table_zindex['+i+']').value;
	}
	return max_zidx;
}

function show_hide_all_but_this_tb(tid){
	if(dbclicked_tb==null) {
		for(var i=0;i<wise_table.length;i++){
			var tmp='tb['+i+']';
			if(tid==tmp) continue;
			
			var pattern = /[\w*]_([^_]+)/; //SJH_MOD
			var str = wise_table[i].name;
			var sub = str.match(pattern);
			if(sub && sub[1]=='vwmldbm'){ //SJH_MOD
				if(document.getElementById('vwmldbm_check').checked==false) continue;
			}			
			document.getElementById(tmp).style.display="inline-block";
		}	
	}
	else {
		for(var i=0;i<wise_table.length;i++){
			var tmp='tb['+i+']';
			if(tid==tmp) continue;				
			document.getElementById(tmp).style.display="none";
		}		
	}
}

function draw_arrow(fno){ //draw a single line 
	fk=fkey_info[fno];
	var tb1=fk.from_table_id;
	var tb2=fk.to_table_id;
	var no=tb1.substring(3,tb1.length-1);
	var no2=tb2.substring(3,tb2.length-1);
	
	if(dbclicked_tb!=null && tb1!=dbclicked_tb && tb2!=dbclicked_tb) {
		return;
	}
// SJH_MOD	
	var pattern = /[\w*]_([^_]+)/;
	var str = wise_table[no].name;
	if(no2.substring(0,3)=='tb[') var str2 = wise_table[no2].name;
	var sub = str.match(pattern);
	if(str2) var sub2 = str2.match(pattern);
	if((sub && sub[1]=='vwmldbm') || (str && str.substring(1,8)=='vwmldbm')){ // SJH_MOD	
		if(document.getElementById(tb1).style.display=='none') return;
	}
	if((sub2 && sub2[1]=='vwmldbm') || (str2 && str2.substring(1,8)=='vwmldbm')){ // SJH_MOD	
		if(document.getElementById(tb2).style.display=='none') return;
	}
	
	document.getElementById(tb1).style.display="inline-block";
	if(str2) document.getElementById(tb2).style.display="inline-block";

	

// to locate the arrow into the exact row	
	var field_y; 
	if(document.getElementById('table_expanded['+no+']').value =='+') field_y=fk.from_y_shrinked;
	else field_y=fk.from_y_expanded;
	
	var from_table_y=parseInt(document.getElementById(tb1).style.top); from_table_y+=field_y*20+34;
	var from_table_x=parseInt(document.getElementById(tb1).style.left); from_table_x+=253;

	try {
		var to_table_y=parseInt(document.getElementById(tb2).style.top); to_table_y +=28;
	} catch (e){}
	try {
		var to_table_x=parseInt(document.getElementById(tb2).style.left); to_table_x -=10;
	} catch (e){}
	if(document.getElementById(tb1))
		var tcolor=document.getElementById(tb1).style.backgroundColor;
	else tcolor="black";
		
	var context=document.getElementById('canvas').getContext('2d');
		context.strokeStyle=tcolor;	
		context.lineWidth=1.5;

// main line
	context.moveTo(from_table_x,from_table_y);context.lineTo(to_table_x,to_table_y);  

// circle shape for from table
	var radius = 4;
	context.beginPath();
	context.arc(from_table_x, from_table_y, radius, 0, 2 * Math.PI, false);
	context.fillStyle = 'green';
	context.fill();
	  
// shape of the arrow head
	var headlen = 10;   // length of head of arrow
    var angle = Math.atan2(to_table_y-from_table_y,to_table_x-from_table_x);
    context.moveTo(from_table_x, from_table_y);
    context.lineTo(to_table_x, to_table_y);
    context.lineTo(to_table_x-headlen*Math.cos(angle-Math.PI/6),to_table_y-headlen*Math.sin(angle-Math.PI/6));
    context.moveTo(to_table_x, to_table_y);
    context.lineTo(to_table_x-headlen*Math.cos(angle+Math.PI/6),to_table_y-headlen*Math.sin(angle+Math.PI/6));	
	
	context.stroke();
}

function draw_all_arrows(){ //called by draw_arrow()
	document.getElementById('arrow_divs').innerHTML="";
	document.getElementById('arrow_divs').innerHTML="<canvas class='arrow' id='canvas' width=3000 height=2500 style='z-index:-10;'></canvas>";
	
	var num_tb=wise_table.length;
	var max_x=0; var max_y=0;
	for(var i=0;i<num_tb;i++) {
		var tmp='tb['+i+']';
		if(parseInt(document.getElementById(tmp).style.left) > max_x)
			max_x=parseInt(document.getElementById(tmp).style.left);
		if(parseInt(document.getElementById(tmp).style.top) > max_y)
			max_y=parseInt(document.getElementById(tmp).style.top);
	}
	document.getElementById('canvas').width=max_x+260;
	document.getElementById('canvas').height=max_y+500;	
	for(var i=0;i<fkey_info.length;i++)
		draw_arrow(i);

}

function get_max_level(){
	var max=0;
	for(var i=0;i<wise_table.length;i++){
		if(wise_table[i].type!='V' && wise_table[i].level>max) max=wise_table[i].level; // view is not counted
	}
	return max;
}

function align_by_DB(){
	var starting_y=60;
	var starting_x=10;
	var y_offset=20;
	var y_mult_factor=21;
	var x_offset=300;
	var k;	
	for(k=0;k<db_nos.length;k++){		
		for(var i=0,y=starting_y;i<wise_table.length;i++){
			if(wise_table[i].type=='V') continue; // skip views
			if(wise_table[i].db_no==db_nos[k]) {
				document.getElementById('tb['+wise_table[i].tb_ID+']').style.left=starting_x+x_offset*k;
				document.getElementById('table_x_pos['+wise_table[i].tb_ID+']').value=starting_x+x_offset*k;
				document.getElementById('tb['+wise_table[i].tb_ID+']').style.top=y+y_offset;
				document.getElementById('table_y_pos['+wise_table[i].tb_ID+']').value=y+y_offset;				
				y+=(parseInt(table_shrunk_y_size_by_table_no[wise_table[i].tb_no])+2)*y_mult_factor;			
			}
		}
		for(var i=0,y=starting_y;i<wise_table.length;i++){
			if(wise_table[i].type=='V' && show_view==true) { // views
				if(wise_table[i].db_no==db_nos[k]) {
					document.getElementById('tb['+wise_table[i].tb_ID+']').style.left=starting_x+x_offset*(k+1);
					document.getElementById('table_x_pos['+wise_table[i].tb_ID+']').value=starting_x+x_offset*(k+1);
					document.getElementById('tb['+wise_table[i].tb_ID+']').style.top=y+y_offset;
					document.getElementById('table_y_pos['+wise_table[i].tb_ID+']').value=y+y_offset;				
					y+=(parseInt(table_shrunk_y_size_by_table_no[wise_table[i].tb_no])+2)*y_mult_factor;			
				}
			}
		}
	}
	draw_all_arrows();
}

function align_by_level(){
	var starting_y=40;
	var starting_x=10;
	var y_offset=20;
	var y_mult_factor=21;
	var x_offset=300;
	var k;
	for(k=0;k<=get_max_level();k++){
		for(var i=0,y=starting_y;i<wise_table.length;i++){ // for tables
			if(wise_table[i].type=='V') continue; // skip views
			if(wise_table[i].level==k) {
				document.getElementById('tb['+wise_table[i].tb_ID+']').style.left=starting_x+x_offset*wise_table[i].level;
				document.getElementById('table_x_pos['+wise_table[i].tb_ID+']').value=starting_x+x_offset*wise_table[i].level;
				
				document.getElementById('tb['+wise_table[i].tb_ID+']').style.top=y+y_offset;
				document.getElementById('table_y_pos['+wise_table[i].tb_ID+']').value=y+y_offset;				
				y+=(parseInt(table_shrunk_y_size_by_table_no[wise_table[i].tb_no])+2)*y_mult_factor;			
			}
		}
		for(var j=0,y=starting_y;j<wise_table.length;j++){ // for views
			if(wise_table[j].type=='V' && show_view==true) { // views
				wise_table[j].level=k+1;
				document.getElementById('tb['+wise_table[j].tb_ID+']').style.left=starting_x+x_offset*wise_table[j].level;
				document.getElementById('tb['+wise_table[j].tb_ID+']').style.display='inline-block';
				document.getElementById('table_x_pos['+wise_table[j].tb_ID+']').value=starting_x+x_offset*wise_table[j].level;
				document.getElementById('tb['+wise_table[j].tb_ID+']').style.top=y+y_offset;
				document.getElementById('table_y_pos['+wise_table[j].tb_ID+']').value=y+y_offset;				
				y+=(parseInt(table_shrunk_y_size_by_table_no[wise_table[j].tb_no])+2)*y_mult_factor;			
			
			}
		}
	}	
	draw_all_arrows();
}

function align_by_sub(){
	var starting_y=40;
	var starting_x=10;
	var y_offset=20;
	var y_mult_factor=21;
	var x_offset=300;	
	var k;
	for(k=0;k<db_nos.length;k++){
	   // get all the subs: eg, wise_user_student_ext => user
	    var wise_table_s=[]; // for sub display
	    var subs=[];
	    var sub_y=[];
	    var pattern = /[\w*]_([^_]+)_[\w+]/;   //SJH_MOD
		for(var i=0;i<wise_table.length;i++){	
			if(wise_table[i].type=='V') continue; // skip views
			var str = wise_table[i].name;
			var sub = str.match(pattern);
			if(sub==null) { // added for vwmldbm
				 var pattern2 = /([^_]+)_[\w+]/;
				 var str = wise_table[i].name;
				 var sub = str.match(pattern2);
			}
			if(sub && subs.indexOf(sub[1])<0) {
				subs.push(sub[1]);
				sub_y.push(starting_y);
			}
			wise_table_s[i]=0;
		}
		//console.log(subs);
		
		for(var i=0,y=starting_y;i<wise_table.length;i++){ // for tables
			if(wise_table[i].type=='V') continue; // skip views
			
			var str = wise_table[i].name;
			var sub = str.match(pattern);	
			if(sub==null) { // added for vwmldbm
				 var pattern2 = /([^_]*)_[\w+]/; //SJH_MOD
				 var str = wise_table[i].name;
				 var sub = str.match(pattern2);
			}			
			if(sub) wise_table_s[i]=parseInt(subs.indexOf(sub[1]));
			else continue;
			y=parseInt(sub_y[subs.indexOf(sub[1])]);
			//console.log('y='+y);
			document.getElementById('tb['+wise_table[i].tb_ID+']').style.left=starting_x+x_offset*wise_table_s[i];
			document.getElementById('table_x_pos['+wise_table[i].tb_ID+']').value=starting_x+x_offset*wise_table_s[i];
			document.getElementById('tb['+wise_table[i].tb_ID+']').style.top=y+y_offset;
			document.getElementById('table_y_pos['+wise_table[i].tb_ID+']').value=y+y_offset;				
			sub_y[subs.indexOf(sub[1])]+=(parseInt(table_shrunk_y_size_by_table_no[wise_table[i].tb_no])+2)*y_mult_factor;			
			
		}
		for(var j=0,y=starting_y;j<wise_table.length;j++){ // for views
			if(wise_table[j].type=='V' && show_view==true) { // views
				wise_table_s[j]=parseInt(sub_y.length);
				//console.log(wise_table_s[j]);
				document.getElementById('tb['+wise_table[j].tb_ID+']').style.left=starting_x+x_offset*wise_table_s[j];
				document.getElementById('tb['+wise_table[j].tb_ID+']').style.display='inline-block';
				document.getElementById('table_x_pos['+wise_table[j].tb_ID+']').value=starting_x+x_offset*wise_table_s[j];
				document.getElementById('tb['+wise_table[j].tb_ID+']').style.top=y+y_offset;
				document.getElementById('table_y_pos['+wise_table[j].tb_ID+']').value=y+y_offset;				
				y+=(parseInt(table_shrunk_y_size_by_table_no[wise_table[j].tb_no])+2)*y_mult_factor;			
			}
		}
	}	
	draw_all_arrows();
}

function show_hide_vwmldbm(fobj){
	for(var i=0;i<wise_table.length;i++){ // for tables
		var pattern2 = /[\w*]_([^_]+)/; //SJH_MOD
		var str = wise_table[i].name;
		var sub = str.match(pattern2);
		if((sub && sub[1]=='vwmldbm') || str.substring(1,8)=='vwmldbm'){ //SJH_MOD			
			if(fobj.checked && dbclicked_tb==null)
				document.getElementById('tb['+wise_table[i].tb_ID+']').style.display="inline-block";
			else {
				document.getElementById('tb['+wise_table[i].tb_ID+']').style.display="none";	
			}
		}
	}	
	draw_all_arrows();
}