import React, {Component} from "React"
import bag from '../assets/images/bag.png'
import bottomWear from '../assets/images/bottom-wear.png'
import book from '../assets/images/book.png'
import puzzle from '../assets/images/puzzle.png'
import setSquare from '../assets/images/set-square.png'

export default class AnimatedIcon extends Component {
    render(){
        return(
            <section className="animated-icon hidden-sm hidden-md hidden-xs">
				<div className="shape-block">
					<ul className="shape-block-listing">
						<li className="common-anime shape-1">
						<img src={bag}  alt="logo"/>
						</li>
						<li className="common-anime shape-2">
							<img src={bottomWear}  alt="logo"/>
						</li>
						<li className="common-anime shape-3">
							<img src={book}  alt="logo"/> 
						</li>
						<li className="common-anime shape-4">
							<img src={puzzle}  alt="logo"/>
						</li>
						<li className="common-anime shape-5">
							<img src={setSquare}  alt="logo"/>
						</li>
						<li className="common-anime shape-6">
							<img src={bag}  alt="logo"/>
						</li>
						<li className="common-anime shape-7">
							<img src={bottomWear}  alt="logo"/>
						</li>
					</ul>
				</div>
			</section>
        )
    }
}