ì
lessS2260ÒParse error at line 14 column 5:

 4:        url(fonts.svg#MyGeometricModern) format("svg");
 5: }
 6: #shorthands {
 7:   background: url("http://www.lesscss.org/spec.html") no-repeat 0 4px;
 8: }
 9: #misc {
10:   background-image: url(images/image.jpg);
11: }
12: #data-uri {
13:   background: url(data:image/png;charset=utf-8;base64,
14:     kiVBORw0KGgoAAAANSUhEUgAAABAAAAAQAQMAAAAlPW0iAAAABlBMVEUAAAD/
        ^
15:     k//+l2Z/dAAAAM0lEQVR4nGP4/5/h/1+G/58ZDrAz3D/McH8yw83NDDeNGe4U
16:     kg9C9zwz3gVLMDA/A6P9/AFGGFyjOXZtQAAAAAElFTkSuQmCC);
17:   background-image: url(data:image/x-png,f9difSSFIIGFIFJD1f982FSDKAA9==);
18:   background-image: url(http://fonts.googleapis.com/css?family=\"Rokkitt\":\(400\),700);
19: }
20: 
21: #svg-data-uri {
22:   background: transparent url('data:image/svg+xml, <svg version="1.1"><g></g></svg>');
23: }
24: 2 A