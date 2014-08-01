var tabLinks = new Array();
var contentDivs = new Array();

function init() {

    // Grab the tab links and content divs from the page
    var tabListItems = document.getElementById('tabs').childNodes;
    for ( var i = 0; i < tabListItems.length; i++) {
        if (tabListItems[i].nodeName == "LI") {
            var tabLink = getFirstChildWithTagName(tabListItems[i], 'A');
            var id = getHash(tabLink.getAttribute('href'));
            tabLinks[id] = tabLink;
            contentDivs[id] = document.getElementById(id);
        }
    }

    // Assign onclick events to the tab links, and
    // highlight the first tab
    var i = 0;

    for ( var id in tabLinks) {
        tabLinks[id].onclick = showTab;
        tabLinks[id].onfocus = function() {
            this.blur()
        };
        if (i == 0)
            tabLinks[id].className = 'selected';
        i++;
    }

    // Hide all content divs except the first
    var i = 0;

    for ( var id in contentDivs) {
        if (i != 0)
            contentDivs[id].className = 'tabContent hide';
        i++;
    }
}

function showTab() {
    var selectedId = getHash(this.getAttribute('href'));

    // Highlight the selected tab, and dim all others.
    // Also show the selected content div, and hide all others.
    for ( var id in contentDivs) {
        if (id == selectedId) {
            tabLinks[id].className = 'selected';
            contentDivs[id].className = 'tabContent';
        } else {
            tabLinks[id].className = '';
            contentDivs[id].className = 'tabContent hide';
        }
    }

    // Stop the browser following the link
    return false;
}

function getFirstChildWithTagName(element, tagName) {
    for ( var i = 0; i < element.childNodes.length; i++) {
        if (element.childNodes[i].nodeName == tagName)
            return element.childNodes[i];
    }
}

function getHash(url) {
    var hashPos = url.lastIndexOf('#');
    return url.substring(hashPos + 1);
}

function changeIMG(id,domain,templatePath,flag){
    switch(flag){
        case 1: // change domain activate icon
            img         = document.getElementById('d_img_OK_' + id + '_' + domain);
            if(img){
                img.src     = templatePath + "/images/iconset/No.png";
                img.id      = 'd_img_NO_' + id + '_' + domain;
            }else{
                img         = document.getElementById('d_img_NO_' + id + '_' + domain);
                img.src     = templatePath + "/images/iconset/OK.png";
                img.id      = 'd_img_OK_' + id + '_' + domain;
            }
            break;
        case 2: // change activate page icon
            img         = document.getElementById('a_img_OK_' + id);
            if(img){
                img.src     = templatePath + "/images/iconset/No.png";
                img.id      = 'a_img_No_' + id;
            }else{
                img         = document.getElementById('a_img_No_' + id );
                img.src     = templatePath + "/images/iconset/OK.png";
                img.id      = 'a_img_OK_' + id;
            }
            break;
        case 3: // change activate sub page icon
            img         = document.getElementById('a_sub_img_OK_' + id);
            if(img){
                img.src     = templatePath + "/images/iconset/No.png";
                img.id      = 'a_sub_img_No_' + id ;
            }else{
                img         = document.getElementById('a_sub_img_No_' + id );
                img.src     = templatePath + "/images/iconset/OK.png";
                img.id      = 'a_sub_img_OK_' + id;
            }
            break;
        case 4:
            img         = document.getElementById('d_sub_img_OK_' + id + '_' + domain);
            if(img){
                img.src     = templatePath + "/images/iconset/No.png";
                img.id      = 'd_sub_img_NO_' + id + '_' + domain;
            }else{
                img         = document.getElementById('d_sub_img_NO_' + id + '_' + domain);
                img.src     = templatePath + "/images/iconset/OK.png";
                img.id      = 'd_sub_img_OK_' + id + '_' + domain;
            }
            break;
    }
}

function addPageToDomain(id,domain,templatePath,flag){
    new Ajax.Request("index.php?modul=admin&action=portal_domainAddPageToDomain&pageID="+id+"&domainID="+domain, {
        method: "post",
        parameters: 'modul=admin',
        //onSuccess: changeIMG(id,domain,templatePath,flag == 1 ? 1 : 4)
        onSuccess: changeIMG(id,domain,templatePath,flag )
    });
}

function activateMainPage(id,templatePath){
    new Ajax.Request("index.php?modul=admin&action=portal_CMSActivate&doit=activeMain&id="+id, {
        method: "post",
        parameters: 'modul=admin',
        onSuccess: changeIMG(id,null,templatePath,2)
    });
}

function activateSubPage(id,templatePath){
    new Ajax.Request("index.php?modul=admin&action=portal_CMSActivate&doit=activeSub&id="+id, {
        method: "post",
        parameters: 'modul=admin',
        onSuccess: changeIMG(id,null,templatePath,3)
    });
}