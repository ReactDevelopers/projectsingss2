Ô
cssS2260»Parse error at line 15 column 5:

 5: #shorthands {
 6:   background: url("http://www.lesscss.org/spec.html?424242") no-repeat 0 4px;
 7:   background: url("img.jpg?424242") center / 100px;
 8:   background: #fff url(image.png?424242) center / 1px 100px repeat-x scroll content-box padding-box;
 9: }
10: #misc {
11:   background-image: url(images/image.jpg?424242);
12: }
13: #data-uri {
14:   background: url(data:image/png;charset=utf-8;base64,
15:     kiVBORw0KGgoAAAANSUhEUgAAABAAAAAQAQMAAAAlPW0iAAAABlBMVEUAAAD/
        ^
16:     k//+l2Z/dAAAAM0lEQVR4nGP4/5/h/1+G/58ZDrAz3D/McH8yw83NDDeNGe4U
17:     kg9C9zwz3gVLMDA/A6P9/AFGGFyjOXZtQAAAAAElFTkSuQmCC);
18:   background-image: url(data:image/x-png,f9difSSFIIGFIFJD1f982FSDKAA9==);
19:   background-image: url(http://fonts.googleapis.com/css?family=\"Rokkitt\":\(400\),700&424242);
20:   background-image: url("http://fonts.googleapis.com/css?family=\"Rokkitt\":\(400\),700&424242");
21: }
22: #svg-data-uri {
23:   background: transparent url('data:image/svg+xml, <svg version="1.1"><g></g></svg>');
24: }
25: .comma-delimited { 2 A