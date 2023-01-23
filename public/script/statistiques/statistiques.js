function initChart(thisWeekRaw, orderedCountRaw) {
    let everydayPurchase = document.getElementById('everydayPurchase');
    let everydayPurchaseChart = new Chart(everydayPurchase, {
        type: "bar",
        data: {
            labels: thisWeekRaw,
            datasets: [{
                data: orderedCountRaw,
                backgroundColor: [
                    'RebeccaPurple',
                    'Tomato',
                    'Yellow',
                    'Orange',
                    'SeaGreen',
                    'SteelBlue',
                    'Brown'
                ],
            }],
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    ticks: {
                        font: {
                            size: 20,
                            family: 'vazir'
                        }
                    }
                },
                y: {
                    ticks: {
                        font: {
                            size: 20,
                            family: 'vazir'
                        }
                    }
                }
            }
        }
    });
}