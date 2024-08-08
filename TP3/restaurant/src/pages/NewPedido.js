import React, { useState, useEffect } from 'react';

function NewPedido() {
  const [itemsMenu, setItemsMenu] = useState([]);
  const [formData, setFormData] = useState({
    id_item_menu: '',
    nro_mesa: '',
    comentarios: '',
  });
  const [mensaje, setMensaje] = useState('');

  useEffect(() => {
    obtenerItemsMenuDeAPI();
  }, []);

  const obtenerItemsMenuDeAPI = async () => {
    try {
      const apiUrl = 'http://localhost:8000/api/items/obtener';
      const response = await fetch(apiUrl);
      const data = await response.json();
      setItemsMenu(data);
    } catch (error) {
      console.error('Error al obtener datos del menú:', error);
    }
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    console.log(e.target.value);
    setFormData({
      ...formData,
      [name]: value,
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const apiUrl = 'http://localhost:8000/api/pedidos/nuevo';
      const response = await fetch(apiUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData),
      });
      if (response.ok) {
        const data = await response.json();
        setMensaje(data.message);
      } else {
        console.error('Error al enviar el formulario:', response.statusText);
        setMensaje('Error al enviar el formulario.');
      }
    } catch (error) {
      console.error('Error en la solicitud POST:', error);
      setMensaje('Error en la solicitud POST.');
    }
  };

  return (
    <div className="NewPedido">
      <h1>Nuevo Pedido</h1>
      {mensaje && <p>{mensaje}</p>}
      <form onSubmit={handleSubmit}>
        <div>
          <label>Ítem del Menú:</label>
          <select name="id_item_menu" value={formData.id_item_menu} onChange={handleInputChange}>
              <option value="">Seleccionar</option>
                  {itemsMenu.map((item) => (
                  <option key={item.id} value={item.id}>
                  {item.nombre}
                  </option>
                   ))}
          </select>
        </div>
        <div>
        <label>Número de Mesa:</label>
        <select name="nro_mesa" value={formData.nro_mesa} onChange={handleInputChange}>
              <option value="">Seleccionar</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6">6</option>
              <option value="7">7</option>
              <option value="8">8</option>
              <option value="9">9</option>
        </select>
      </div>
        <div>
          <label>Comentarios:</label>
          <textarea name="comentarios" value={formData.comentarios} onChange={handleInputChange}></textarea>
        </div>
        <button type="submit">Crear Pedido</button>
      </form>
    </div>
  );
}

export default NewPedido;
