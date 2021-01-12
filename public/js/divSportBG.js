'use strict';

var elements = document.getElementsByClassName('groupCard');
/**
 * Match the image to the category
 */
function divSportBGFunc() {
    for (let i = 0; i < elements.length; i++) {
        switch (elements[i].getAttribute('name')) {
            case '1':
                elements[i].style.backgroundImage = "url(/img/footballDivBg.jpg)";
                break;
            case '2':
                elements[i].style.backgroundImage = "url(/img/basketballDivBg.jpg)";
                break;
            case '3':
                elements[i].style.backgroundImage = "url(/img/tennisDivBg.jpg)";
                break;
            case '4':
                elements[i].style.backgroundImage = "url(/img/runDivBg.png)";
                break;
            case '5':
                elements[i].style.backgroundImage = "url(/img/gymDivBg.png)";
                break;
            default:
        }
    }
}
divSportBGFunc(); //on load