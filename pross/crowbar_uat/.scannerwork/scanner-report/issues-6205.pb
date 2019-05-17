Î
cssS2260µParse error at line 25 column 5:

15:   not-a-comment: url(//z);
16: }
17: #shorthands {
18:   background: url("http://www.lesscss.org/spec.html") no-repeat 0 4px;
19: }
20: #misc {
21:   background-image: url(http://localhost:8081/test/browser/less/images/image.jpg);
22: }
23: #data-uri {
24:   background: url(data:image/png;charset=utf-8;base64,
25:     kiVBORw0KGgoAAAANSUhEUgAAABAAAAAQAQMAAAAlPW0iAAAABlBMVEUAAAD/
        ^
26:     k//+l2Z/dAAAAM0lEQVR4nGP4/5/h/1+G/58ZDrAz3D/McH8yw83NDDeNGe4U
27:     kg9C9zwz3gVLMDA/A6P9/AFGGFyjOXZtQAAAAAElFTkSuQmCC);
28:   background-image: url(data:image/x-png,f9difSSFIIGFIFJD1f982FSDKAA9==);
29:   background-image: url(http://fonts.googleapis.com/css?family=\"Rokkitt\":\(400\),700);
30: }
31: #svg-data-uri {
32:   background: transparent url('data:image/svg+xml, <svg version="1.1"><g></g></svg>');
33: }
34: .comma-delimited {
35:   background: url(http://localhost:8081/test/browser/less/bg.jpg) no-repeat, url(http://localhost:8081/test/browser/less/bg.png) repeat-x top left, url(http://localhost:8081/test/browser/less/bg); 2 A