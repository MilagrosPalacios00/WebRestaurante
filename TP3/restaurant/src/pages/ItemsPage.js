import React, { useState, useEffect } from 'react';
import NewItem from './NewItem'
import EditItem from './EditItem';
import '../ItemsPage.css';


function ItemsPage() {
  const [mostrarNewItem, setMostrarNewItem] = useState(false);
  const [mostrarEditItem, setMostrarEditItem] = useState(false);
  const [editItemId, setEditItemId] = useState(null);
  const [apiData, setApiData] = useState(null);
  const [nombre, setNombre] = useState('');
  const [tipo, setTipo] = useState('');
  const [orden, setOrden] = useState('');
  const [mensajeError, setMensajeError] = useState('');

  const obtenerDatosDeAPI = async () => {
    try {
      const apiUrl = 'http://localhost:8000/api/items/obtener';
      const response = await fetch(apiUrl);
      const data = await response.json();
      setApiData(data);
    } catch (error) {
      console.error('Error al obtener datos de la API:', error);
    }
  };

  const obtenerItemsConFiltros = async () => {
    const apiUrl = `http://localhost:8000/api/items/obtener?nombre=${nombre}&tipo=${tipo}&orden=${orden}`;
    try {
      const response = await fetch(apiUrl);
      if (response.ok) {
        const data = await response.json();
        setApiData(data);
      } else {
        console.error('Error al obtener items:', response.statusText);
      }
    } catch (error) {
      console.error('Error al obtener items:', error);
    }
}

useEffect(() => {
  obtenerDatosDeAPI();
  obtenerItemsConFiltros();
}, [nombre, tipo, orden]);


    const handleNombreChange = (e) => {
      setNombre(e.target.value);
    };
  
    const handleTipoChange = (e) => {
      setTipo(e.target.value);
    };
  
    const handleOrdenChange = (e) => {
      setOrden(e.target.value);
    };
  

  const handleDelete = async (id) => {
    const confirmacion = window.confirm(`¿Seguro que quieres eliminar?`);
    if (!confirmacion) {
      return; 
    }
    try {
      const apiUrl = `http://localhost:8000/api/items/delete/${id}`;
      const response = await fetch(apiUrl, {
        method: 'DELETE',
      });

      if (response.ok) {
        obtenerDatosDeAPI();
      } else {
        console.error('Error al eliminar el elemento:', response.statusText);
        setMensajeError(`Error al eliminar el elemento: ${response.statusText}`);
      }
    } catch (error) {
      console.error('Error en la solicitud de eliminación:', error);
      setMensajeError(`Error en la solicitud de eliminación: ${error.message}`);
    }
  };

  const handleEdit = (id) => {
    // Mostrar el componente EditItem y establecer el ID del ítem a editar
    setMostrarNewItem(false);
    setMostrarEditItem(true);
    setEditItemId(id);
  };

  const convertirDatosAHtml = (data) => {
    if (!data || data.length === 0) {
      return <p>No hay datos disponibles.</p>;
    }

    const elementosHtml = data.map((item, index) => (
      <div key={index} className="comida-items">
        <div className="item">
          <h2>{item.nombre}</h2>
          <h3>Precio: {item.precio}</h3>
          {item.imagen && (
                <div>
                  <img
                    src={`data:image/png;base64,${item.imagen}`}
                  />
                </div>
              )}          
          {/* Botón Eliminar para cada elemento */}
          <button onClick={() => handleDelete(item.id)}>Eliminar</button>
        {/* Nuevo botón para editar el ítem */}
        <button onClick={() => handleEdit(item.id)}>Editar</button>
        </div>
      </div>
    ));

    return <div className="menu">{elementosHtml}</div>;
  };

  const handleCrearNuevoItem = () => {
    setMostrarNewItem(true);
  };

  const handleFiltrar = () => {
    obtenerItemsConFiltros();
  };

  const handleLimpiarFiltros = () => {
    setNombre('');
    setTipo('');
    setOrden('ASC');
    obtenerDatosDeAPI();
  };

  return (
    <div className="PageItems">
      <div>
      <button onClick={handleCrearNuevoItem}>Crear Nuevo Item</button>       
      {/* Filtros */}
      <div>
        <label>Nombre:</label>
        <input type="text" value={nombre} onChange={handleNombreChange} />
      </div>
      <div>
      <label>Tipo:</label>
            <select value={tipo} onChange={handleTipoChange}>
            <option value="">Seleccionar</option>
            <option value="Comida">Comida</option>
            <option value="Bebida">Bebida</option>
      </select>
      </div>

      <div>
        <label>Orden:</label>
        <select value={orden} onChange={handleOrdenChange}>
          <option value="ASC">Ascendente</option>
          <option value="DESC">Descendente</option>
        </select>
      </div>
      <button onClick={handleFiltrar}>Filtrar</button>
      <button onClick={handleLimpiarFiltros}>Limpiar Filtros</button> 
        {mostrarNewItem ? (
          <NewItem />
        ) : mostrarEditItem ? (
          <EditItem itemId={editItemId} />
        ) : (
          apiData ? convertirDatosAHtml(apiData) : <p>Cargando datos...</p>
        )}
      </div>
    </div>
  );
}
export default ItemsPage;