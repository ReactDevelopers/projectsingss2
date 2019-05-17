­	
cssS2260”	Parse error at line 21 column 5:

11:   src: local(Futura-Medium), url(https://www.github.com/cloudhead/less.js/fonts.svg#MyGeometricModern) format("svg");
12: }
13: #shorthands {
14:   background: url("http://www.lesscss.org/spec.html") no-repeat 0 4px;
15: }
16: #misc {
17:   background-image: url(https://www.github.com/cloudhead/less.js/images/image.jpg);
18: }
19: #data-uri {
20:   background: url(data:image/png;charset=utf-8;base64,
21:     kiVBORw0KGgoAAAANSUhEUgAAABAAAAAQAQMAAAAlPW0iAAAABlBMVEUAAAD/
        ^
22:     k//+l2Z/dAAAAM0lEQVR4nGP4/5/h/1+G/58ZDrAz3D/McH8yw83NDDeNGe4U
23:     kg9C9zwz3gVLMDA/A6P9/AFGGFyjOXZtQAAAAAElFTkSuQmCC);
24:   background-image: url(data:image/x-png,f9difSSFIIGFIFJD1f982FSDKAA9==);
25:   background-image: url(http://fonts.googleapis.com/css?family=\"Rokkitt\":\(400\),700);
26: }
27: #svg-data-uri {
28:   background: transparent url('data:image/svg+xml, <svg version="1.1"><g></g></svg>');
29: }
30: .comma-delimited {
31:   background: url(https://www.github.com/cloudhead/less.js/bg.jpg) no-repeat, url(https://www.github.com/cloudhead/less.js/bg.png) repeat-x top left, url(https://www.github.com/cloudhead/less.js/bg); 2 A