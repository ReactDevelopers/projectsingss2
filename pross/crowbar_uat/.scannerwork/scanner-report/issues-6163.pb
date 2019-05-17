×
lessS2260½Parse error at line 17 column 5:

 7: }
 8: #shorthands {
 9:   background: url("http://www.lesscss.org/spec.html") no-repeat 0 4px;
10: }
11: #misc {
12:   background-image: url(images/image.jpg);
13:   background: url("#inline-svg");
14: }
15: #data-uri {
16:   background: url(data:image/png;charset=utf-8;base64,
17:     kiVBORw0KGgoAAAANSUhEUgAAABAAAAAQAQMAAAAlPW0iAAAABlBMVEUAAAD/
        ^
18:     k//+l2Z/dAAAAM0lEQVR4nGP4/5/h/1+G/58ZDrAz3D/McH8yw83NDDeNGe4U
19:     kg9C9zwz3gVLMDA/A6P9/AFGGFyjOXZtQAAAAAElFTkSuQmCC);
20:   background-image: url(data:image/x-png,f9difSSFIIGFIFJD1f982FSDKAA9==);
21:   background-image: url(http://fonts.googleapis.com/css?family=\"Rokkitt\":\(400\),700);
22: }
23: 
24: #svg-data-uri {
25:   background: transparent url('data:image/svg+xml, <svg version="1.1"><g></g></svg>');
26: }
27: 2 A