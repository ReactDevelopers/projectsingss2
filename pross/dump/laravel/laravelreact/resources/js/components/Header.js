import React from 'react'
import { Link } from 'react-router-dom'

const Header = () => (
  <nav className='navbar navbar-expand-md navbar-light navbar-laravel'>
    <div className='container'>
      <Link className='navbar-brand' to='/'>Home11</Link>
    </div>
    <Link className='navbar-brand' to='create'>Login1</Link>
    <Link className='navbar-brand' to='register'>SignUp</Link>
    <Link className='navbar-brand' to='weather'>Weather</Link>
    <Link className='navbar-brand' to='googlemap'>Map</Link>
  </nav>
)

export default Header