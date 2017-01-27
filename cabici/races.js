var serverURL = 'http://cabici.net/api';

function insertResults(race, link) {

    resultdivid = 'results' + race;
    resulttoggleid = 'restoggle' + race;

    // if the results div doesn't already exist
    if (jQuery('#'+resultdivid).size() == 0) {
        // add a table row for the results to live in
        jQuery(link).parents('tr').after('<tr id='+ resultdivid + '><td colspan=3><div></div></td></tr>');
        generateResultTable(race, jQuery('#' + resultdivid).find('div'));
    }
    container = jQuery('#'+resultdivid);

    if (jQuery('#'+resulttoggleid).html() == "View Results") {
        jQuery(container).show();
        jQuery('#'+ resulttoggleid).html("Hide Results");
    } else {
        jQuery(container).hide();
        jQuery('#'+ resulttoggleid).html("View Results");
    }
}

function generateResultTable(race, target) {

    jQuery(target).html("Loading...");
    jQuery.getJSON(serverURL + '/raceresults?race=' + race,
        function(data) {
            grades = [];
            tables = '<div id="'+resultdivid+'">';
            tables += "<p>Total riders: " + data.length + ".</p>";

            for (var i=0; i < data.length; i++) {
                grade = data[i].grade;
                gradeIndex = null;
                for (var j=0; j < grades.length; j++) {
                    if (grades[j].name === grade) {
                        gradeIndex = j;
                        break;
                    }
                }
                if (gradeIndex === null) {
                    gradeIndex = grades.length;
                    grades.push({
                        'name': grade,
                        'riderCount': 0,
                        'riders': [],
                    });
                }
                grades[gradeIndex].riderCount++;
                if (data[i].place !== null && data[i].place !== 0) {
                    grades[gradeIndex].riders.push({
                        'number': data[i].number,
                        'place': data[i].place,
                        'rider': data[i].rider,
                        'club': data[i].club
                    });
                }
            }

            for (var i=0; i < grades.length; i++) {
                grade = grades[i];

                tables += '<h4>' + grade.name + ' Grade (' + grade.riderCount + ' riders)</h4>';
                tables += '<table class="racetable hovertable" ><tr><th>Place</th><th>Rider</th><th>Club</th></tr>';

                riders = grade.riders;

                for (var j=0; j < riders.length; j++) {
                    rider = riders[j];
                    tables += '<tr><td>' + rider.place + '</td><td>' + rider.rider + '</td><td>' + rider.club + '</td></tr>';
                }

                tables += '</table></div>';
            }
            jQuery(target).html(tables);
        });
    }
