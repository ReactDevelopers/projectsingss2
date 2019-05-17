import React from "react"

const displayName = "HomePageHeader"

function Header() {
  return <header className="bg-primary text-white">
    <div className="container text-center">
      <img width="125" height="125" src="/laravel/larareact/public/img/manish.jpeg" alt="..." className="rounded-circle" />
      <h1>Manish Mahant</h1>
      <p className="lead">Electronics & Communication</p>
      <p className="lead">Fullstack Developer at&nbsp;
        <a className="text-white"
           href="http://www.techjack.biz"
           target="_blank"
           rel="noreferrer noopener">techjack.biz</a>
      </p>
      <p className="lead"><i className="fa fa-heart text-danger" />{`{ PHP, JavaScript, Node, MySQL, MongoDB }`}</p>
    </div>
  </header>
}
Header.displayName = displayName

export default Header
