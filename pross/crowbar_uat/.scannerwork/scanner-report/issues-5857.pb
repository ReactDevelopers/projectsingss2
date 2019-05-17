…
lessS2260éParse error at line 137 column 7:

127: }
128: .test {
129:   &:nth-child(@{num}) {
130:     selector: interpolated;
131:   }
132:   &:nth-child(odd):not(:nth-child(3)) {
133:     color: #ff0000;
134:   }
135:  }
136: [prop],
137: [prop=10%],
           ^
138: [prop="value@{num}"],
139: [prop*="val@{num}"],
140: [|prop~="val@{num}"],
141: [*|prop$="val@{num}"],
142: [ns|prop^="val@{num}"],
143: [@{num}^="val@{num}"],
144: [@{num}=@{num}],
145: [@{num}] {
146:   attributes: yes;
147: } 2‰‰ 