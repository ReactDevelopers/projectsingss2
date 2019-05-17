¶
cssS2260çParse error at line 15 column 5:

 5:   src: local(Futura-Medium), url(folder\ \(1\)/fonts.svg#MyGeometricModern) format("svg");
 6: }
 7: #shorthands {
 8:   background: url("http://www.lesscss.org/spec.html") no-repeat 0 4px;
 9: }
10: #misc {
11:   background-image: url(folder\ \(1\)/images/image.jpg);
12: }
13: #data-uri {
14:   background: url(data:image/png;charset=utf-8;base64,
15:     kiVBORw0KGgoAAAANSUhEUgAAABAAAAAQAQMAAAAlPW0iAAAABlBMVEUAAAD/
        ^
16:     k//+l2Z/dAAAAM0lEQVR4nGP4/5/h/1+G/58ZDrAz3D/McH8yw83NDDeNGe4U
17:     kg9C9zwz3gVLMDA/A6P9/AFGGFyjOXZtQAAAAAElFTkSuQmCC);
18:   background-image: url(data:image/x-png,f9difSSFIIGFIFJD1f982FSDKAA9==);
19:   background-image: url(http://fonts.googleapis.com/css?family=\"Rokkitt\":\(400\),700);
20: }
21: #svg-data-uri {
22:   background: transparent url('data:image/svg+xml, <svg version="1.1"><g></g></svg>');
23: }
24: .comma-delimited {
25:   background: url(folder\ \(1\)/bg.jpg) no-repeat, url(folder\ \(1\)/bg.png) repeat-x top left, url(folder\ \(1\)/bg); 2 A