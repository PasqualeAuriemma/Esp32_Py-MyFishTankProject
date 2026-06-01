    var opts_temp = {
    angle: -0.2, // The span of the gauge arc
    lineWidth: 0.2, // The line thickness
    radiusScale: 1, // Relative radius
    pointer: {
        length: 0.6, // // Relative to gauge radius
        strokeWidth: 0.053, // The thickness
        color: '#000000' // Fill color
    },
    limitMax: false, // If false, max value increases automatically if value > maxValue
    limitMin: false, // If true, the min value of the gauge will be fixed
    colorStart: '#6F6EA0', // Colors
    colorStop: '#C0C0DB', // just experiment with them
    strokeColor: '#EEEEEE', // to see which ones work best for you
    generateGradient: true,
    highDpiSupport: true, // High resolution support
    staticZones: [
        //{ strokeStyle: "#FF0000", min: 10, max: 15 }, // Red from 10 to 15
        //{ strokeStyle: "#FFDD00", min: 15, max: 22 }, // Yellow
        { strokeStyle: "#FFFFFF", min: 10, max: 32 }, // Green
        { strokeStyle: "#FFDD00", min: 30, max: 35 }, // Yellow
        { strokeStyle: "#FF0000", min: 35, max: 40 } // Red
    ],

    staticLabels: {
        font: "10px sans-serif", // Specifies font
        labels: [10, 15, 20, 30, 35, 40], // Print labels at these values
        color: "#d5dee2", // Optional: Label text color
        fractionDigits: 0 // Optional: Numerical precision. 0=round off.
    },
    renderTicks: {
        divisions: 5,
        divWidth: 1.1,
        divLength: 0.7,
        divColor: '#333333',
        subDivisions: 3,
        subLength: 0.5,
        subWidth: 0.6,
        subColor: '#666666'
    }
};

var target_temp = document.getElementById('temperature_gauge'); // your canvas element
var gauge_temp = new Gauge(target_temp).setOptions(opts_temp); // create sexy gauge!
gauge_temp.maxValue = 40; // set max gauge value
gauge_temp.minValue = 10;
//gauge_temp.setMinValue(15); // Prefer setter over gauge.minValue = 0
gauge_temp.animationSpeed = 32; // set animation speed (32 is default value)
gauge_temp.set(20); // set actual value
gauge_temp.setTextField(document.getElementById('gauge-value_temp'));

var opts_ec = {
    angle: -0.2, // The span of the gauge arc
    lineWidth: 0.2, // The line thickness
    radiusScale: 1, // Relative radius
    pointer: {
        length: 0.6, // // Relative to gauge radius
        strokeWidth: 0.053, // The thickness
        color: '#000000' // Fill color
    },
    limitMax: false, // If false, max value increases automatically if value > maxValue
    limitMin: false, // If true, the min value of the gauge will be fixed
    colorStart: '#6F6EA0', // Colors
    colorStop: '#C0C0DB', // just experiment with them
    strokeColor: '#EEEEEE', // to see which ones work best for you
    generateGradient: true,
    highDpiSupport: true, // High resolution support
    staticZones: [
        //{ strokeStyle: "#FF0000", min: 10, max: 15 }, // Red from 10 to 15
        //{ strokeStyle: "#FFDD00", min: 15, max: 22 }, // Yellow
        { strokeStyle: "#FFFFFF", min: 0, max: 600 }, // Green
        { strokeStyle: "#FFDD00", min: 600, max: 750 }, // Yellow
        { strokeStyle: "#FF0000", min: 750, max: 1000 } // Red
    ],

    staticLabels: {
        font: "10px sans-serif", // Specifies font
        labels: [0, 200, 400, 600, 800, 1000], // Print labels at these values
        color: "#d5dee2", // Optional: Label text color
        fractionDigits: 0 // Optional: Numerical precision. 0=round off.
    },
    renderTicks: {
        divisions: 5,
        divWidth: 1.8,
        divLength: 0.7,
        divColor: '#333333',
        subDivisions: 4,
        subLength: 0.5,
        subWidth: 0.6,
        subColor: '#666666'
    }
};

var target_ec = document.getElementById('ec_gauge'); // your canvas element
var gauge_ec = new Gauge(target_ec).setOptions(opts_ec); // create sexy gauge!
gauge_ec.maxValue = 1000; // set max gauge value
gauge_ec.minValue = 0;
//gauge_ec.setMinValue(15); // Prefer setter over gauge.minValue = 0
gauge_ec.animationSpeed = 32; // set animation speed (32 is default value)
gauge_ec.set(565); // set actual value
gauge_ec.setTextField(document.getElementById('gauge-value_ec'));

var opts_ph = {
    angle: -0.2, // The span of the gauge arc
    lineWidth: 0.2, // The line thickness
    radiusScale: 1, // Relative radius
    pointer: {
        length: 0.6, // // Relative to gauge radius
        strokeWidth: 0.053, // The thickness
        color: '#000000' // Fill color
    },
    limitMax: false, // If false, max value increases automatically if value > maxValue
    limitMin: false, // If true, the min value of the gauge will be fixed
    colorStart: '#6F6EA0', // Colors
    colorStop: '#C0C0DB', // just experiment with them
    strokeColor: '#EEEEEE', // to see which ones work best for you
    generateGradient: true,
    highDpiSupport: true, // High resolution support
    staticZones: [
        //{ strokeStyle: "#FF0000", min: 10, max: 15 }, // Red from 10 to 15
        //{ strokeStyle: "#FFDD00", min: 15, max: 22 }, // Yellow
        { strokeStyle: "#FF0000", min: 3, max: 5.5 }, // Red
        { strokeStyle: "#FFDD00", min: 5.5, max: 6.3 }, // Yellow
        { strokeStyle: "#FFFFFF", min: 6.3, max: 7.3 }, // White
        { strokeStyle: "#FFDD00", min: 7.3, max: 8 }, // Yellow
        { strokeStyle: "#FF0000", min: 8, max: 10 } // Red
    ],

    staticLabels: {
        font: "10px sans-serif", // Specifies font
        labels: [3, 4, 5, 6, 7, 8, 9, 10], // Print labels at these values
        color: "#d5dee2", // Optional: Label text color
        fractionDigits: 0 // Optional: Numerical precision. 0=round off.
    },
    renderTicks: {
        divisions: 7,
        divWidth: 1.9,
        divLength: 0.7,
        divColor: '#333333',
        subDivisions: 3,
        subLength: 0.5,
        subWidth: 0.6,
        subColor: '#666666'
    }
};

var target_ph = document.getElementById('ph_gauge'); // your canvas element
var gauge_ph = new Gauge(target_ph).setOptions(opts_ph); // create sexy gauge!
gauge_ph.maxValue = 10; // set max gauge value
gauge_ph.minValue = 3;
//gauge_ph.setMinValue(15); // Prefer setter over gauge.minValue = 0
gauge_ph.animationSpeed = 32; // set animation speed (32 is default value)
gauge_ph.set(6); // set actual value
gauge_ph.setTextField(document.getElementById('gauge-value_ph'));
