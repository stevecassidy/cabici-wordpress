var serverURL = 'http://test.cabici.net/api';

function insertResults(race, container) {
    $.ajax(serverURL + '/raceresults?race=' + race,
    {
        'success': function(data) {
            grades = [];
            tables = '';
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
                        'rider': data[i].rider.user.first_name + ' ' + data[i].rider.user.last_name,
                        'club': data[i].rider.club.name,
                    });
                }
            }

            for (var i=0; i < grades.length; i++) {
                grade = grades[i];

                tables += '<h3>' + grade.name + ' Grade (' + grade.riderCount + ' riders)</h3>';
                tables += '<table><tr><td>Number</td><td>Place</td><td>Rider</td><td>Club</td></tr>';

                riders = grade.riders;

                for (var j=0; j < riders.length; j++) {
                    rider = riders[j];
                    tables += '<tr><td>' + rider.number + '</td><td>' + rider.place + '</td><td>' + rider.rider + '</td><td>' + rider.club + '</td></tr>';
                }

                tables += '</table>';
            }
            $(container).html(tables);
        }
    });
}
