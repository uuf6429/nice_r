function nice_r_toggle(pfx, id) {
    var elp = document.getElementById(pfx + '_v' + id);
    var elc = document.getElementById(pfx + '_a' + id);
    if (elp) {
        if (elp.style.display === 'block') {
            elp.style.display = 'none';
            if (elc) elc.innerHTML = '&#9658;';
        } else {
            elp.style.display = 'block';
            if (elc) elc.innerHTML = '&#9660;';
        }
    }
}
