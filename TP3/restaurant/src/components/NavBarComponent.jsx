import React, { useState } from 'react';
import '../NavBarComponent.css';
import ItemsPage from '../pages/ItemsPage';
import PedidosPage from '../pages/PedidosPage';

const NavBarComponent = () => {
  const [currentSection, setCurrentSection] = useState('home');

  const handleHomeClick = () => {
    setCurrentSection('home');
    window.location.reload(); // Recargar la página al hacer clic en "Home"
  };

  const renderSection = () => {
    switch (currentSection) {
      case 'home':
        return <ItemsPage />;
      case 'pedidos':
        return <PedidosPage />;
      default:
        return null;
    }
  };

  return (
    <div>
      <nav>
        <ul>
          <li>
            <a href="#" onClick={handleHomeClick}>
              Home
            </a>
          </li>
          <li>
            <a href="#" onClick={() => setCurrentSection('pedidos')}>
              Pedidos
            </a>
          </li>
        </ul>
      </nav>

      {renderSection()}
    </div>
  );
};

export default NavBarComponent;
