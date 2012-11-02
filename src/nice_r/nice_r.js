function nice_r_toggle(id){
	var el = document.getElementById('nice_r_v'+id);
	if(el){
		if(el.style.display==='block'){
			el.style.display = 'none';
			document.getElementById('nice_r_a'+id).innerHTML = '&#9658;';
		}else{
			el.style.display = 'block';
			document.getElementById('nice_r_a'+id).innerHTML = '&#9660;';
		}
	}
}