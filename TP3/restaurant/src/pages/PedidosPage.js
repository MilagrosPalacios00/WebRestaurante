import React, { useState, useEffect } from 'react';
import NewPedido from './NewPedido'; 



function PedidoPage() {
  const [pedidos, setPedidos] = useState([]);
  const [mensaje, setMensaje] = useState('');
  const [mostrarNewPedido, setMostrarNewPedido] = useState(false);

  useEffect(() => {
    obtenerPedidosDeAPI();
  }, []);

  const obtenerPedidosDeAPI = async () => {
    try {
      const apiUrl = 'http://localhost:8000/api/pedidos/obtener';
      const response = await fetch(apiUrl);
      const data = await response.json();
      setPedidos(data);
    } catch (error) {
      console.error('Error al obtener datos de la API:', error);
    }
  };

  const handleDelete = async (id) => {
    const confirmacion = window.confirm('¿Seguro que quieres eliminar este pedido?');
    if (!confirmacion) {
      return;
    }

    try {
      const apiUrl = `http://localhost:8000/api/pedidos/delete/${id}`;
      const response = await fetch(apiUrl, {
        method: 'DELETE',
      });

      if (response.ok) {
        obtenerPedidosDeAPI();
        setMensaje('Pedido eliminado con éxito.');
      } else {
        console.error('Error al eliminar el pedido:', response.statusText);
        setMensaje('Error al eliminar el pedido.');
      }
    } catch (error) {
      console.error('Error en la solicitud de eliminación:', error);
      setMensaje('Error en la solicitud de eliminación.');
    }
  };

  const handleCrearNuevoPedido = () => {
    // Setear el estado para mostrar la página NewPedido
    setMostrarNewPedido(true);
  };

  return (
    <div className="PedidoPage">
      <h1>Listado de Pedidos</h1>
      {mensaje && <p>{mensaje}</p>}
      {mostrarNewPedido ? (
        <NewPedido />
      ) : (
        <>
          {pedidos.length === 0 ? (
            <p>No hay pedidos disponibles.</p>
          ) : (
            <ul>
              {pedidos.map((pedido) => (
                <li key={pedido.id}>
                  <div>
                    <h3>{pedido.nombre}</h3>
                    {pedido.imagen && (
                      <div>
                        <img src={`data:image/png;base64, ${pedido.imagen}`} />
                      </div>
                    )}
                    <p>idPedido: {pedido.pedido_id}</p>
                    <p>Precio: {pedido.precio}</p>
                    <p>Fecha: {pedido.fechaAlta}</p>
                    <p>Mesa: {pedido.nromesa}</p>
                    <p>Comentarios: {pedido.comentarios}</p>

                    <button onClick={() => handleDelete(pedido.pedido_id)}>Eliminar</button>
                  </div>
                </li>
              ))}
            </ul>
          )}
          <button onClick={handleCrearNuevoPedido}>Crear Nuevo Pedido</button>
        </>
      )}
    </div>
  );
}

export default PedidoPage;
