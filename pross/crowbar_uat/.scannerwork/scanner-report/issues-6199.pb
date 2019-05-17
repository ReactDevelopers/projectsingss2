ç	
cssS2260ÙParse error at line 22 column 5:

12: }
13: #shorthands {
14:   background: url("http://www.lesscss.org/spec.html") no-repeat 0 4px;
15: }
16: #misc {
17:   background-image: url(http://localhost:8081/test/browser/less/relative-urls/images/image.jpg);
18:   background: url("#inline-svg");
19: }
20: #data-uri {
21:   background: url(data:image/png;charset=utf-8;base64,
22:     kiVBORw0KGgoAAAANSUhEUgAAABAAAAAQAQMAAAAlPW0iAAAABlBMVEUAAAD/
        ^
23:     k//+l2Z/dAAAAM0lEQVR4nGP4/5/h/1+G/58ZDrAz3D/McH8yw83NDDeNGe4U
24:     kg9C9zwz3gVLMDA/A6P9/AFGGFyjOXZtQAAAAAElFTkSuQmCC);
25:   background-image: url(data:image/x-png,f9difSSFIIGFIFJD1f982FSDKAA9==);
26:   background-image: url(http://fonts.googleapis.com/css?family=\"Rokkitt\":\(400\),700);
27: }
28: #svg-data-uri {
29:   background: transparent url('data:image/svg+xml, <svg version="1.1"><g></g></svg>');
30: }
31: .comma-delimited {
32:   background: url(http://localhost:8081/test/browser/less/relative-urls/bg.jpg) no-repeat, url(http://localhost:8081/test/browser/less/relative-urls/bg.png) repeat-x top left, url(http://localhost:8081/test/browser/less/relative-urls/bg); 2 A