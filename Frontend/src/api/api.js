const BASE_URL = "http://localhost/backend/api";

const request = async (endpoint, method = "GET", body = null) => {
  const headers = {
    "Content-Type": "application/json",
  };

  // Construire l'URL avec les paramètres pour les requêtes GET
  let url = `${BASE_URL}${endpoint}`;
  if (method === "GET" && body) {
    const params = new URLSearchParams(body).toString();
    url += `?${params}`;
  }

  const config = {
    method,
    headers,
    credentials: "include",
  };

  if (body && method !== "GET") {
    config.body = JSON.stringify(body);
  }

  const response = await fetch(url, config);
  let data;
  try {
    data = await response.json();
  } catch (e) {
    throw new Error("Réponse invalide du serveur");
  }

  if (!response.ok) {
    throw new Error(data.error || "Erreur réseau");
  }

  return data;
};

export async function getJPOs() {
  return await request("/jpo.php");
}

export async function addJPO(data) {
  return await request("/jpo.php", "POST", data);
}

export async function updateJPO(data) {
  return await request("/jpo.php", "PUT", data);
}

export async function deleteJPO(id) {
  return await request("/jpo.php", "DELETE", { id });
}

export async function registerJPO(jpoId, userName, userEmail) {
  return await request("/inscription.php", "POST", {
    jpo_id: jpoId,
    user_name: userName,
    user_email: userEmail,
  });
}

export async function unregisterJPO(jpoId, userEmail) {
  return await request("/inscription.php", "DELETE", {
    jpo_id: jpoId,
    user_email: userEmail,
  });
}

export async function getInscriptions(jpoId) {
  return await request("/inscription.php", "GET", { jpo_id: jpoId });
}

export async function addComment(jpoId, userName, comment) {
  return await request("/comment.php", "POST", {
    jpo_id: jpoId,
    user_name: userName,
    comment,
  });
}

export async function getComments(jpoId) {
  return await request("/comment.php", "GET", { jpo_id: jpoId });
}

export async function getAllComments() {
  return await request("/comment.php", "GET", {});
}

export async function approveComment(commentId) {
  return await request("/comment.php", "PUT", { comment_id: commentId });
}

export async function deleteComment(commentId) {
  return await request("/comment.php", "DELETE", { comment_id: commentId });
}

export async function login(username, password) {
  return await request("/auth.php", "POST", { username, password });
}

export async function sendReminder(data) {
  return await request("/email.php", "POST", data);
}

export async function getStats(jpoId) {
  return await request("/stats.php", "GET", { jpo_id: jpoId });
}

export async function getContent(key) {
  const data = await request("/content.php", "GET", { key });
  return data.value;
}

export async function updateContent(key, value) {
  return await request("/content.php", "POST", { key, value });
}
